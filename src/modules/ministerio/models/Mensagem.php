<?php

/**
 * Model para Mensagens do Ministério
 * Gerencia operações CRUD e envio de mensagens
 */
class MensagemModel
{
    private static $pdo;
    
    public static function initConnection()
    {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
                'heriberto',
                '0631'
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }

    /**
     * Criar mensagem
     */
    public static function criar($dados)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            INSERT INTO ministerio_mensagens 
            (ministerio_id, reuniao_id, tipo, assunto, conteudo, template, canal, status, data_agendamento, criado_por) 
            VALUES (:ministerio_id, :reuniao_id, :tipo, :assunto, :conteudo, :template, :canal, :status, :data_agendamento, :criado_por)
        ");
        
        $result = $stmt->execute([
            ':ministerio_id' => $dados['ministerio_id'],
            ':reuniao_id' => $dados['reuniao_id'] ?? null,
            ':tipo' => $dados['tipo'] ?? 'geral',
            ':assunto' => $dados['assunto'],
            ':conteudo' => $dados['conteudo'],
            ':template' => $dados['template'] ?? null,
            ':canal' => $dados['canal'] ?? 'email',
            ':status' => $dados['status'] ?? 'rascunho',
            ':data_agendamento' => $dados['data_agendamento'] ?? null,
            ':criado_por' => $dados['criado_por']
        ]);
        
        if ($result) {
            return $pdo->lastInsertId();
        }
        
        return false;
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
     * Listar todas as mensagens
     */
    public static function list()
    {
        $pdo = self::initConnection();
        $stmt = $pdo->query("
            SELECT m.*, 
                   min.nome as ministerio_nome,
                   u.name as criador_nome
            FROM ministerio_mensagens m
            LEFT JOIN ministerios min ON m.ministerio_id = min.id
            LEFT JOIN users u ON m.criado_por = u.id
            ORDER BY m.criado_em DESC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Criar mensagem (alias para criar)
     */
    public static function create($dados)
    {
        return self::criar($dados);
    }
    
    /**
     * Atualizar mensagem
     */
    public static function update($id, $dados)
    {
        $pdo = self::initConnection();
        $set = [];
        
        if (isset($dados['assunto'])) {
            $set[] = "assunto = :assunto";
        }
        if (isset($dados['conteudo'])) {
            $set[] = "conteudo = :conteudo";
        }
        if (isset($dados['canal'])) {
            $set[] = "canal = :canal";
        }
        if (isset($dados['status'])) {
            $set[] = "status = :status";
        }
        if (isset($dados['data_agendamento'])) {
            $set[] = "data_agendamento = :data_agendamento";
        }
        
        if (empty($set)) {
            return false;
        }
        
        $sql = "UPDATE ministerio_mensagens SET " . implode(', ', $set) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        $params = [':id' => $id];
        if (isset($dados['assunto'])) $params[':assunto'] = $dados['assunto'];
        if (isset($dados['conteudo'])) $params[':conteudo'] = $dados['conteudo'];
        if (isset($dados['canal'])) $params[':canal'] = $dados['canal'];
        if (isset($dados['status'])) $params[':status'] = $dados['status'];
        if (isset($dados['data_agendamento'])) $params[':data_agendamento'] = $dados['data_agendamento'];
        
        return $stmt->execute($params);
    }
    
    /**
     * Excluir mensagem
     */
    public static function delete($id)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("DELETE FROM ministerio_mensagens WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Buscar por ID (alias)
     */
    public static function findById($id)
    {
        return self::buscarPorId($id);
    }
    
    /**
     * Listar destinatários
     */
    public static function listRecipients($ministerioId)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.id, u.name as nome, u.email
            FROM users u
            INNER JOIN ministerio_membros mm ON u.id = mm.membro_id
            WHERE mm.ministerio_id = :ministerio_id 
              AND mm.ativo = 1
            ORDER BY u.name
        ");
        $stmt->execute([':ministerio_id' => $ministerioId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Gerar preview
     */
    public static function generatePreview($conteudo, $canal)
    {
        // Substituir variáveis de template
        $preview = str_replace([
            '{nome}', '{email}', '{telefone}'
        ], [
            'João Silva', 'joao@exemplo.com', '(11) 99999-9999'
        ], $conteudo);
        
        // Formatar according ao canal
        switch ($canal) {
            case 'email':
                return nl2br($preview);
            case 'whatsapp':
                return nl2br($preview);
            case 'sms':
                return substr($preview, 0, 160) . (strlen($preview) > 160 ? '...' : '');
            default:
                return $preview;
        }
    }
    
    /**
     * Cancelar mensagem
     */
    public static function cancel($id)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            UPDATE ministerio_mensagens 
            SET status = 'rascunho', data_agendamento = NULL
            WHERE id = :id AND status = 'agendado'
        ");
        
        return $stmt->execute([':id' => $id]);
    }
    /**
     * Buscar mensagem por ID
     */
    public static function buscarPorId($id)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("SELECT * FROM ministerio_mensagens WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

