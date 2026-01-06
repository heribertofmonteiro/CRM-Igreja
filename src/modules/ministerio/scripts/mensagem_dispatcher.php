<?php
/**
 * Script de Dispatcher de Mensagens
 * Executar via cron: a cada 5 minutos
 */
require_once __DIR__ . '/../../../Include/Config.php';
require_once __DIR__ . '/../../../Include/Functions.php';
require_once __DIR__ . '/../models/Mensagem.php';

use ChurchCRM\Utils\LoggerUtils;
use ChurchCRM\dto\SystemConfig;

$logger = LoggerUtils::getAppLogger();
$logger->info('Iniciando processo de envio de mensagens');

$rateLimit = 50; // mensagens por minuto
$logger->info('Rate limit configurado: ' . $rateLimit . ' mensagens/minuto');
$enviosProcessados = 0;

// Processar mensagens agendadas
$mensagensAgendadas = MensagemModel::obterMensagensAgendadas();
foreach ($mensagensAgendadas as $mensagem) {
    // Mudar status para enviando
    $sql = "UPDATE ministerio_mensagens SET status = 'enviando' WHERE id = " . (int)$mensagem['id'];
    RunQuery($sql);
    
    // Criar registros de envio se não existirem
    $sql = "SELECT COUNT(*) as total FROM ministerio_mensagens_envio WHERE mensagem_id = " . (int)$mensagem['id'];
    $result = RunQuery($sql);
    $row = CRM_mysqli_fetch_assoc($result);
    
    if ($row['total'] == 0) {
        require_once __DIR__ . '/../models/Reuniao.php';
        require_once __DIR__ . '/../models/Ministerio.php';
        MensagemModel::criarRegistrosEnvio($mensagem['id'], $mensagem['ministerio_id'], $mensagem['reuniao_id'] ?? null);
    }
}

// Processar envios pendentes (limite de rate)
$enviosPendentes = MensagemModel::obterEnviosPendentes($rateLimit);

foreach ($enviosPendentes as $envio) {
    if ($enviosProcessados >= $rateLimit) {
        break;
    }
    
    try {
        $sucesso = false;
        $erro = null;
        
        switch ($envio['canal']) {
            case 'email':
                $sucesso = enviarEmail($envio);
                break;
            case 'whatsapp':
                $sucesso = enviarWhatsApp($envio);
                break;
            case 'sms':
                $sucesso = enviarSMS($envio);
                break;
            case 'interno':
                $sucesso = salvarMensagemInterna($envio);
                break;
        }
        
        if ($sucesso) {
            MensagemModel::atualizarStatusEnvio($envio['id'], 'enviado');
            $logger->info("Mensagem enviada: envio_id=" . $envio['id']);
            $enviosProcessados++;
        } else {
            if ($envio['tentativas'] >= 3) {
                MensagemModel::atualizarStatusEnvio($envio['id'], 'falhou', $erro);
                $logger->warning('Envio falhou permanentemente: envio_id=' . $envio['id'] . ' erro=' . ($erro ?? 'n/d'));
            } else {
                MensagemModel::atualizarStatusEnvio($envio['id'], 'pendente', $erro);
                $logger->info('Envio reprogramado: envio_id=' . $envio['id'] . ' tentativa=' . (($envio['tentativas'] ?? 0) + 1));
            }
        }
        
    } catch (\Exception $e) {
        $logger->error("Erro ao enviar mensagem envio_id=" . $envio['id'] . ": " . $e->getMessage());
        MensagemModel::atualizarStatusEnvio($envio['id'], 'falhou', $e->getMessage());
    }
}

// Atualizar status das mensagens
$sql = "UPDATE ministerio_mensagens m
        SET m.status = 'enviado', m.data_envio = NOW()
        WHERE m.id IN (
            SELECT DISTINCT mensagem_id FROM ministerio_mensagens_envio 
            WHERE status = 'enviado'
        )
        AND m.status = 'enviando'
        AND NOT EXISTS (
            SELECT 1 FROM ministerio_mensagens_envio e2 
            WHERE e2.mensagem_id = m.id AND e2.status IN ('pendente', 'enviando')
        )";
RunQuery($sql);

$logger->info("Processo de envio concluído. Mensagens processadas: $enviosProcessados");

// Funções auxiliares
function enviarEmail($envio) {
    global $logger;
    
    if (empty($envio['per_Email'])) {
        $logger->warning("Email não disponível para pessoa ID: " . $envio['destinatario_id']);
        return false;
    }
    
    // Usar PHPMailer do projeto
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configurações SMTP do sistema
        $mail->isSMTP();
        $mail->Host = SystemConfig::getValue('sSMTPHost');
        $mail->SMTPAuth = true;
        $mail->Username = SystemConfig::getValue('sSMTPUser');
        $mail->Password = SystemConfig::getValue('sSMTPPass');
        $mail->SMTPSecure = SystemConfig::getValue('sSMTPProtocol');
        $mail->Port = SystemConfig::getValue('iSMTPPort');
        
        $mail->setFrom(SystemConfig::getValue('sFromEmailAddress'), SystemConfig::getValue('sChurchName'));
        $mail->addAddress($envio['per_Email'], $envio['per_FirstName'] . ' ' . $envio['per_LastName']);
        
        $mail->isHTML(true);
        $mail->Subject = $envio['assunto'];
        
        // Processar template se existir
        $conteudo = $envio['conteudo'];
        if (!empty($envio['template'])) {
            $dados = [
                'nome' => $envio['per_FirstName'] . ' ' . $envio['per_LastName'],
                'titulo_reuniao' => $envio['titulo_reuniao'] ?? '',
                'data_reuniao' => $envio['data_reuniao'] ?? '',
            ];
            $conteudo = MensagemModel::processarTemplate($envio['template'], $dados);
        }
        
        $mail->Body = nl2br($conteudo);
        $mail->AltBody = strip_tags($conteudo);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        $logger->error("Erro PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}

function enviarWhatsApp($envio) {
    global $logger;
    // Implementar integração com Twilio/Zenvia
    // Por enquanto, retornar false
    $logger->warning("Envio via WhatsApp ainda não implementado");
    return false;
}

function enviarSMS($envio) {
    global $logger;
    // Implementar integração com provedor SMS
    $logger->warning("Envio via SMS ainda não implementado");
    return false;
}

function salvarMensagemInterna($envio) {
    // Salvar mensagem no sistema interno (notificações)
    return true;
}


