<?php
/**
 * Script de Lembrete de Reuniões
 * Executar via cron: a cada hora
 */
require_once __DIR__ . '/../../../Include/Config.php';
require_once __DIR__ . '/../../../Include/Functions.php';
require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Mensagem.php';
require_once __DIR__ . '/../models/Ministerio.php';

use ChurchCRM\Utils\LoggerUtils;
use ChurchCRM\dto\SystemURLs;

$logger = LoggerUtils::getAppLogger();
$logger->info('Iniciando processo de lembrete de reuniões');

// Buscar reuniões que precisam de lembrete (24h antes)
$reunioes = ReuniaoModel::obterReunioesParaLembrete();

foreach ($reunioes as $reuniao) {
    try {
        $participantes = ReuniaoModel::listarParticipantes($reuniao['id']);
        
        foreach ($participantes as $participante) {
            // Criar mensagem de lembrete
            $conteudo = "Lembrete: Reunião do ministério " . $reuniao['ministerio_nome'] . "\n\n";
            $conteudo .= "Título: " . $reuniao['titulo'] . "\n";
            $conteudo .= "Data: " . date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) . "\n";
            if (!empty($reuniao['local'])) {
                $conteudo .= "Local: " . $reuniao['local'] . "\n";
            }
            $conteudo .= "\nLink para confirmar presença: " . SystemURLs::getRootPath() . "/v2/ministerio/reuniao/rsvp/" . $participante['token_rsvp'];
            
            $dadosMensagem = [
                'ministerio_id' => $reuniao['ministerio_id'],
                'reuniao_id' => $reuniao['id'],
                'tipo' => 'lembrete',
                'assunto' => 'Lembrete: ' . $reuniao['titulo'],
                'conteudo' => $conteudo,
                'canal' => 'email',
                'status' => 'agendado',
                'data_agendamento' => date('Y-m-d H:i:s'),
                'criado_por' => $reuniao['criado_por']
            ];
            
            MensagemModel::criar($dadosMensagem);
        }
        
        $logger->info("Lembretes criados para reunião ID: " . $reuniao['id']);
        
    } catch (\Exception $e) {
        $logger->error("Erro ao criar lembrete para reunião ID " . $reuniao['id'] . ": " . $e->getMessage());
    }
}

$logger->info('Processo de lembrete de reuniões concluído');


