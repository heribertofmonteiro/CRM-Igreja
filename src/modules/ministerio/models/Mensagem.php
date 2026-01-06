<?php
/**
 * Model: Mensagem
 */

require_once __DIR__ . '/Ministerio.php';
require_once __DIR__ . '/Reuniao.php';

class MensagemModel
{
    /**
     * Criar mensagem
     */
    public static function criar($dados)
    {
        $ministerioId = (int)$dados['ministerio_id'];
        $reuniaoId = !empty($dados['reuniao_id']) ? (int)$dados['reuniao_id'] : 'NULL';
        $tipo = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['tipo'] ?? 'geral');
        $assunto = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['assunto']);
        $conteudo = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['conteudo']);
        $template = !empty($dados['template']) ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['template']) . "'" : 'NULL';
        $canal = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['canal'] ?? 'email');
        $status = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['status'] ?? 'rascunho');
        $dataAgendamento = !empty($dados['data_agendamento']) ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['data_agendamento']) . "'" : 'NULL';
        $criadoPor = (int)$dados['criado_por'];
        
        $sql = "INSERT INTO ministerio_mensagens 
                (ministerio_id, reuniao_id, tipo, assunto, conteudo, template, canal, status, data_agendamento, criado_por) 
                VALUES ($ministerioId, $reuniaoId, '$tipo', '$assunto', '$conteudo', $template, '$canal', '$status', $dataAgendamento, $criadoPor)";
        
        RunQuery($sql);
        $mensagemId = CRM_mysqli_insert_id($GLOBALS['cnInfoCentral']);
        
        // Se não for rascunho, criar registros de envio
        if ($status !== 'rascunho') {
            self::criarRegistrosEnvio($mensagemId, $ministerioId, $reuniaoId);
        }
        
        return $mensagemId;
    }
    
    /**
     * Criar registros de envio para cada membro
     */
    public static function criarRegistrosEnvio($mensagemId, $ministerioId, $reuniaoId = null)
    {
        // Se houver reunião, enviar apenas para participantes
        if ($reuniaoId) {
            $participantes = ReuniaoModel::listarParticipantes($reuniaoId);
            foreach ($participantes as $participante) {
                self::criarRegistroEnvio($mensagemId, $participante['membro_id']);
            }
        } else {
            // Enviar para todos os membros do ministério
            $membros = MinisterioModel::listarMembros($ministerioId);
            foreach ($membros as $membro) {
                self::criarRegistroEnvio($mensagemId, $membro['membro_id']);
            }
        }
    }
    
    /**
     * Criar registro individual de envio
     */
    private static function criarRegistroEnvio($mensagemId, $destinatarioId)
    {
        $mensagem = self::buscarPorId($mensagemId);
        $canal = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $mensagem['canal']);
        
        $sql = "INSERT INTO ministerio_mensagens_envio 
                (mensagem_id, destinatario_id, canal, status) 
                VALUES ($mensagemId, " . (int)$destinatarioId . ", '$canal', 'pendente')";
        
        RunQuery($sql);
    }
    
    /**
     * Buscar mensagem por ID
     */
    public static function buscarPorId($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM ministerio_mensagens WHERE id = $id";
        $result = RunQuery($sql);
        return CRM_mysqli_fetch_assoc($result);
    }
    
    /**
     * Obter mensagens agendadas para envio
     */
    public static function obterMensagensAgendadas()
    {
        $sql = "SELECT * FROM ministerio_mensagens 
                WHERE status = 'agendado' 
                AND data_agendamento <= NOW()";
        
        $result = RunQuery($sql);
        $mensagens = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $mensagens[] = $row;
        }
        
        return $mensagens;
    }
    
    /**
     * Obter envios pendentes
     */
    public static function obterEnviosPendentes($limite = 50)
    {
        $limite = (int)$limite;
        $sql = "SELECT e.*, m.assunto, m.conteudo, m.template, m.canal,
                p.per_FirstName, p.per_LastName, p.per_Email, p.per_CellPhone
                FROM ministerio_mensagens_envio e
                JOIN ministerio_mensagens m ON e.mensagem_id = m.id
                JOIN person_per p ON e.destinatario_id = p.per_ID
                WHERE e.status = 'pendente'
                ORDER BY e.criado_em ASC
                LIMIT $limite";
        
        $result = RunQuery($sql);
        $envios = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $envios[] = $row;
        }
        
        return $envios;
    }
    
    /**
     * Atualizar status de envio
     */
    public static function atualizarStatusEnvio($envioId, $status, $erro = null)
    {
        $envioId = (int)$envioId;
        $status = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $status);
        $erro = $erro ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $erro) . "'" : 'NULL';
        
        $sql = "UPDATE ministerio_mensagens_envio 
                SET status = '$status', 
                    tentativas = tentativas + 1,
                    erro = $erro,
                    data_tentativa = NOW()";
        
        if ($status === 'enviado') {
            $sql .= ", data_envio = NOW()";
        }
        
        $sql .= " WHERE id = $envioId";
        
        RunQuery($sql);
    }
    
    /**
     * Processar template de mensagem
     */
    public static function processarTemplate($template, $dados)
    {
        $placeholders = [
            '{{nome}}' => $dados['nome'] ?? '',
            '{{titulo_reuniao}}' => $dados['titulo_reuniao'] ?? '',
            '{{data_reuniao}}' => $dados['data_reuniao'] ?? '',
            '{{local}}' => $dados['local'] ?? '',
            '{{link_rsvp}}' => $dados['link_rsvp'] ?? '',
        ];
        
        $mensagem = $template;
        foreach ($placeholders as $placeholder => $valor) {
            $mensagem = str_replace($placeholder, $valor, $mensagem);
        }
        
        return $mensagem;
    }
    
    /**
     * Listar mensagens
     */
    public static function listar($filtros = [])
    {
        $sql = "SELECT m.*, 
                min.nome as ministerio_nome,
                r.titulo as reuniao_titulo,
                p.per_FirstName as criador_nome, p.per_LastName as criador_sobrenome
                FROM ministerio_mensagens m
                JOIN ministerios min ON m.ministerio_id = min.id
                LEFT JOIN ministerio_reunioes r ON m.reuniao_id = r.id
                LEFT JOIN person_per p ON m.criado_por = p.per_ID
                WHERE 1=1";
        
        if (!empty($filtros['ministerio_id'])) {
            $sql .= " AND m.ministerio_id = " . (int)$filtros['ministerio_id'];
        }
        
        if (!empty($filtros['reuniao_id'])) {
            $sql .= " AND m.reuniao_id = " . (int)$filtros['reuniao_id'];
        }
        
        if (!empty($filtros['status'])) {
            $status = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['status']);
            $sql .= " AND m.status = '$status'";
        }
        
        if (!empty($filtros['tipo'])) {
            $tipo = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['tipo']);
            $sql .= " AND m.tipo = '$tipo'";
        }
        
        $sql .= " ORDER BY m.criado_em DESC LIMIT 1000";
        
        $result = RunQuery($sql);
        $mensagens = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $mensagens[] = $row;
        }
        
        return $mensagens;
    }
    
    /**
     * Obter histórico de mensagens de um ministério ou reunião
     */
    public static function obterHistorico($ministerioId = null, $reuniaoId = null)
    {
        $sql = "SELECT m.*, 
                COUNT(DISTINCT e.id) as total_envios,
                SUM(CASE WHEN e.status = 'enviado' THEN 1 ELSE 0 END) as enviados,
                SUM(CASE WHEN e.status = 'falhou' THEN 1 ELSE 0 END) as falhos,
                SUM(CASE WHEN e.status = 'pendente' THEN 1 ELSE 0 END) as pendentes
                FROM ministerio_mensagens m
                LEFT JOIN ministerio_mensagens_envio e ON m.id = e.mensagem_id
                WHERE 1=1";
        
        if ($ministerioId) {
            $sql .= " AND m.ministerio_id = " . (int)$ministerioId;
        }
        
        if ($reuniaoId) {
            $sql .= " AND m.reuniao_id = " . (int)$reuniaoId;
        }
        
        $sql .= " GROUP BY m.id ORDER BY m.criado_em DESC";
        
        $result = RunQuery($sql);
        $mensagens = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $mensagens[] = $row;
        }
        
        return $mensagens;
    }
}

