<?php
/**
 * Model: Reunião
 */

require_once __DIR__ . '/Ministerio.php';

class ReuniaoModel
{
    /**
     * Listar reuniões
     */
    public static function listar($ministerioId = null, $apenasFuturas = false)
    {
        $sql = "SELECT r.*, 
                m.nome as ministerio_nome,
                p.per_FirstName as criador_nome, p.per_LastName as criador_sobrenome,
                (SELECT COUNT(*) FROM ministerio_reunioes_participantes rp WHERE rp.reuniao_id = r.id) as total_participantes
                FROM ministerio_reunioes r
                JOIN ministerios m ON r.ministerio_id = m.id
                JOIN person_per p ON r.criado_por = p.per_ID
                WHERE 1=1";
        
        if ($ministerioId) {
            $sql .= " AND r.ministerio_id = " . (int)$ministerioId;
        }
        
        if ($apenasFuturas) {
            $sql .= " AND r.data_reuniao > NOW() AND r.ativo = 1";
        }
        
        $sql .= " ORDER BY r.data_reuniao DESC";
        
        $result = RunQuery($sql);
        $reunioes = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $reunioes[] = $row;
        }
        
        return $reunioes;
    }
    
    /**
     * Buscar reunião por ID
     */
    public static function buscarPorId($id)
    {
        $id = (int)$id;
        $sql = "SELECT r.*, 
                m.nome as ministerio_nome,
                p.per_FirstName as criador_nome, p.per_LastName as criador_sobrenome
                FROM ministerio_reunioes r
                JOIN ministerios m ON r.ministerio_id = m.id
                JOIN person_per p ON r.criado_por = p.per_ID
                WHERE r.id = $id";
        
        $result = RunQuery($sql);
        return CRM_mysqli_fetch_assoc($result);
    }
    
    /**
     * Criar reunião
     */
    public static function criar($dados)
    {
        $ministerioId = (int)$dados['ministerio_id'];
        $titulo = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['titulo']);
        $descricao = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['descricao'] ?? '');
        $dataReuniao = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['data_reuniao']);
        $local = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['local'] ?? '');
        $criadoPor = (int)$dados['criado_por'];
        
        $sql = "INSERT INTO ministerio_reunioes (ministerio_id, titulo, descricao, data_reuniao, local, criado_por, ativo) 
                VALUES ($ministerioId, '$titulo', '$descricao', '$dataReuniao', '$local', $criadoPor, 1)";
        
        RunQuery($sql);
        $reuniaoId = CRM_mysqli_insert_id($GLOBALS['cnInfoCentral']);
        
        // Adicionar participantes automaticamente (membros do ministério)
        self::adicionarParticipantesAutomaticos($reuniaoId, $ministerioId);
        
        return $reuniaoId;
    }
    
    /**
     * Adicionar participantes automaticamente baseado nos membros do ministério
     */
    private static function adicionarParticipantesAutomaticos($reuniaoId, $ministerioId)
    {
        $membros = MinisterioModel::listarMembros($ministerioId);
        
        foreach ($membros as $membro) {
            $token = bin2hex(random_bytes(32));
            $sql = "INSERT INTO ministerio_reunioes_participantes 
                    (reuniao_id, membro_id, status, token_rsvp) 
                    VALUES ($reuniaoId, " . (int)$membro['membro_id'] . ", 'pendente', '$token')
                    ON DUPLICATE KEY UPDATE token_rsvp = '$token'";
            RunQuery($sql);
        }
    }
    
    /**
     * Listar participantes da reunião
     */
    public static function listarParticipantes($reuniaoId)
    {
        $reuniaoId = (int)$reuniaoId;
        $sql = "SELECT rp.*, 
                p.per_FirstName, p.per_LastName, p.per_Email, p.per_CellPhone
                FROM ministerio_reunioes_participantes rp
                JOIN person_per p ON rp.membro_id = p.per_ID
                WHERE rp.reuniao_id = $reuniaoId
                ORDER BY p.per_FirstName, p.per_LastName";
        
        $result = RunQuery($sql);
        $participantes = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $participantes[] = $row;
        }
        
        return $participantes;
    }
    
    /**
     * Confirmar presença via token RSVP
     */
    public static function confirmarRSVP($token, $status = 'confirmado')
    {
        $token = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $token);
        $status = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $status);
        
        $sql = "UPDATE ministerio_reunioes_participantes 
                SET status = '$status', data_confirmacao = NOW() 
                WHERE token_rsvp = '$token'";
        
        RunQuery($sql);
        return CRM_mysqli_affected_rows($GLOBALS['cnInfoCentral']) > 0;
    }
    
    /**
     * Buscar participante por token
     */
    public static function buscarParticipantePorToken($token)
    {
        $token = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $token);
        $sql = "SELECT rp.*, r.titulo, r.data_reuniao, r.local, r.ministerio_id,
                m.nome as ministerio_nome,
                p.per_FirstName, p.per_LastName
                FROM ministerio_reunioes_participantes rp
                JOIN ministerio_reunioes r ON rp.reuniao_id = r.id
                JOIN ministerios m ON r.ministerio_id = m.id
                JOIN person_per p ON rp.membro_id = p.per_ID
                WHERE rp.token_rsvp = '$token'";
        
        $result = RunQuery($sql);
        return CRM_mysqli_fetch_assoc($result);
    }
    
    /**
     * Obter reuniões que precisam de lembrete (24h antes)
     */
    public static function obterReunioesParaLembrete()
    {
        $sql = "SELECT r.*, m.nome as ministerio_nome
                FROM ministerio_reunioes r
                JOIN ministerios m ON r.ministerio_id = m.id
                WHERE r.ativo = 1 
                AND r.data_reuniao BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
                AND r.id NOT IN (
                    SELECT DISTINCT mensagem.reuniao_id 
                    FROM ministerio_mensagens mensagem 
                    WHERE mensagem.tipo = 'lembrete' 
                    AND mensagem.status = 'enviado'
                    AND DATE(mensagem.data_envio) = CURDATE()
                )";
        
        $result = RunQuery($sql);
        $reunioes = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $reunioes[] = $row;
        }
        
        return $reunioes;
    }
    
    /**
     * Atualizar reunião
     */
    public static function atualizar($id, $dados)
    {
        $id = (int)$id;
        $ministerioId = (int)$dados['ministerio_id'];
        $titulo = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['titulo']);
        $descricao = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['descricao'] ?? '');
        $dataReuniao = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['data_reuniao']);
        $local = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['local'] ?? '');
        $ativo = isset($dados['ativo']) ? (int)$dados['ativo'] : 1;
        
        $sql = "UPDATE ministerio_reunioes 
                SET ministerio_id = $ministerioId, 
                    titulo = '$titulo', 
                    descricao = '$descricao', 
                    data_reuniao = '$dataReuniao', 
                    local = '$local',
                    ativo = $ativo,
                    atualizado_em = NOW()
                WHERE id = $id";
        
        RunQuery($sql);
        return true;
    }
    
    /**
     * Excluir reunião (soft delete)
     */
    public static function excluir($id)
    {
        $id = (int)$id;
        $sql = "UPDATE ministerio_reunioes SET ativo = 0, atualizado_em = NOW() WHERE id = $id";
        RunQuery($sql);
        return true;
    }

    /**
     * Adicionar participante à reunião
     */
    public static function adicionarParticipante($reuniaoId, $pessoaId, $funcao = 'participante')
    {
        $reuniaoId = (int)$reuniaoId;
        $pessoaId = (int)$pessoaId;
        $funcao = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $funcao);
        $token = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO ministerio_reunioes_participantes 
                (reuniao_id, membro_id, funcao, status, token_rsvp) 
                VALUES ($reuniaoId, $pessoaId, '$funcao', 'pendente', '$token')
                ON DUPLICATE KEY UPDATE funcao = '$funcao'";
        
        RunQuery($sql);
        return CRM_mysqli_affected_rows($GLOBALS['cnInfoCentral']) > 0;
    }

    /**
     * Remover participante da reunião
     */
    public static function removerParticipante($reuniaoId, $pessoaId)
    {
        $reuniaoId = (int)$reuniaoId;
        $pessoaId = (int)$pessoaId;
        
        $sql = "DELETE FROM ministerio_reunioes_participantes 
                WHERE reuniao_id = $reuniaoId AND membro_id = $pessoaId";
        
        RunQuery($sql);
        return CRM_mysqli_affected_rows($GLOBALS['cnInfoCentral']) > 0;
    }

    /**
     * Confirmar presença (wrapper para confirmarRSVP)
     */
    public static function confirmarPresenca($reuniaoId, $pessoaId, $status = 'confirmado')
    {
        $reuniaoId = (int)$reuniaoId;
        $pessoaId = (int)$pessoaId;
        $status = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $status);
        
        $sql = "UPDATE ministerio_reunioes_participantes 
                SET status = '$status', data_confirmacao = NOW() 
                WHERE reuniao_id = $reuniaoId AND membro_id = $pessoaId";
        
        RunQuery($sql);
        return CRM_mysqli_affected_rows($GLOBALS['cnInfoCentral']) > 0;
    }
}

