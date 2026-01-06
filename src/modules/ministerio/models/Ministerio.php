<?php
/**
 * Model: Ministerio
 */


class MinisterioModel
{
    /**
     * Listar todos os ministérios
     */
    public static function listar()
    {
        $sql = "SELECT m.*, p.per_FirstName as lider_nome, p.per_LastName as lider_sobrenome
                FROM ministerios m
                LEFT JOIN person_per p ON m.lider_id = p.per_ID
                WHERE m.ativo = 1
                ORDER BY m.nome ASC";
        
        $result = RunQuery($sql);
        $ministerios = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $ministerios[] = $row;
        }
        
        return $ministerios;
    }

    /**
     * Buscar ministério por ID
     */
    public static function buscarPorId($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM ministerios WHERE id = $id";
        $result = RunQuery($sql);
        return CRM_mysqli_fetch_assoc($result);
    }

    /**
     * Criar novo ministério
     */
    public static function criar($dados)
    {
        $nome = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['nome']);
        $descricao = isset($dados['descricao']) ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['descricao']) . "'" : 'NULL';
        $liderId = (int)$dados['lider_id'];
        $coordenadorId = isset($dados['coordenador_id']) ? (int)$dados['coordenador_id'] : 'NULL';
        $ativo = isset($dados['ativo']) && $dados['ativo'] ? 1 : 0;

        $sql = "INSERT INTO ministerios (nome, descricao, lider_id, coordenador_id, ativo) 
                VALUES ('$nome', $descricao, $liderId, $coordenadorId, $ativo)";
        
        RunQuery($sql);
        return CRM_mysqli_insert_id($GLOBALS['cnInfoCentral']);
    }

    /**
     * Atualizar ministério
     */
    public static function atualizar($id, $dados)
    {
        $id = (int)$id;
        $nome = CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['nome']);
        $descricao = isset($dados['descricao']) ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['descricao']) . "'" : 'NULL';
        $liderId = (int)$dados['lider_id'];
        $coordenadorId = isset($dados['coordenador_id']) ? (int)$dados['coordenador_id'] : 'NULL';
        $ativo = isset($dados['ativo']) && $dados['ativo'] ? 1 : 0;

        $sql = "UPDATE ministerios SET
                nome = '$nome',
                descricao = $descricao,
                lider_id = $liderId,
                coordenador_id = $coordenadorId,
                ativo = $ativo
                WHERE id = $id";
        
        RunQuery($sql);
    }

    /**
     * Excluir ministério
     */
    public static function excluir($id)
    {
        $id = (int)$id;
        // Soft delete could be an option here, but for now, we do a hard delete.
        // First, delete members associated with the ministry.
        $sql_delete_members = "DELETE FROM ministerio_membros WHERE ministerio_id = $id";
        RunQuery($sql_delete_members);

        // Then, delete the ministry itself.
        $sql = "DELETE FROM ministerios WHERE id = $id";
        RunQuery($sql);
    }

    /**
     * Listar membros de um ministério
     */
    public static function listarMembros($ministerioId)
    {
        $ministerioId = (int)$ministerioId;
        $sql = "SELECT mm.*, p.per_FirstName, p.per_LastName, p.per_Email
                FROM ministerio_membros mm
                JOIN person_per p ON mm.membro_id = p.per_ID
                WHERE mm.ministerio_id = $ministerioId AND mm.ativo = 1
                ORDER BY p.per_FirstName, p.per_LastName";
        
        $result = RunQuery($sql);
        $membros = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $membros[] = $row;
        }
        
        return $membros;
    }

    /**
     * Adicionar membro a um ministério
     */
    public static function adicionarMembro($ministerioId, $membroId, $funcao)
    {
        $ministerioId = (int)$ministerioId;
        $membroId = (int)$membroId;
        $funcao = "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $funcao) . "'";
        $dataEntrada = "'" . date('Y-m-d') . "'";

        $sql = "INSERT INTO ministerio_membros (ministerio_id, membro_id, funcao, data_entrada)
                VALUES ($ministerioId, $membroId, $funcao, $dataEntrada)";
        
        RunQuery($sql);
        return CRM_mysqli_insert_id($GLOBALS['cnInfoCentral']);
    }

    /**
     * Remover membro de um ministério
     */
    public static function removerMembro($membroMinisterioId)
    {
        $membroMinisterioId = (int)$membroMinisterioId;
        // This is a soft delete, setting the member as inactive
        $sql = "UPDATE ministerio_membros SET ativo = 0, data_saida = NOW() WHERE id = $membroMinisterioId";
        RunQuery($sql);
    }
    
    /**
     * Atualizar dados de um membro no ministério
     */
    public static function atualizarMembro($membroMinisterioId, $dados)
    {
        $membroMinisterioId = (int)$membroMinisterioId;
        $funcao = isset($dados['funcao']) ? "'" . CRM_mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $dados['funcao']) . "'" : 'NULL';
        $ativo = isset($dados['ativo']) ? (int)$dados['ativo'] : 1;

        $sql = "UPDATE ministerio_membros SET
                funcao = $funcao,
                ativo = $ativo
                WHERE id = $membroMinisterioId";
        
        RunQuery($sql);
    }

    /**
     * Listar pessoas que não são membros do ministério
     */
    public static function listarPessoasNaoMembros($ministerioId)
    {
        $ministerioId = (int)$ministerioId;
        $sql = "SELECT p.per_ID, p.per_FirstName, p.per_LastName, p.per_Email
                FROM person_per p
                WHERE p.per_ID NOT IN (
                    SELECT mm.membro_id 
                    FROM ministerio_membros mm 
                    WHERE mm.ministerio_id = $ministerioId AND mm.ativo = 1
                )
                ORDER BY p.per_FirstName, p.per_LastName";
        
        $result = RunQuery($sql);
        $pessoas = [];
        
        while ($row = CRM_mysqli_fetch_assoc($result)) {
            $pessoas[] = $row;
        }
        
        return $pessoas;
    }

    /**
     * Contar total de ministérios
     */
    public static function contarTotal($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM ministerios WHERE 1=1";
        
        if (isset($filters['ativo'])) {
            $ativo = (int)$filters['ativo'];
            $sql .= " AND ativo = $ativo";
        }
        
        $result = RunQuery($sql);
        $row = CRM_mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }

    /**
     * Contar total de membros de um ministério
     */
    public static function contarTotalMembros($ministerioId)
    {
        $ministerioId = (int)$ministerioId;
        $sql = "SELECT COUNT(*) as total 
                FROM ministerio_membros 
                WHERE ministerio_id = $ministerioId AND ativo = 1";
        
        $result = RunQuery($sql);
        $row = CRM_mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }

    /**
     * Contar reuniões próximas de um ministério
     */
    public static function contarProximas($ministerioId)
    {
        $ministerioId = (int)$ministerioId;
        $sql = "SELECT COUNT(*) as total 
                FROM ministerio_reunioes 
                WHERE ministerio_id = $ministerioId 
                AND data_reuniao > NOW() 
                AND ativo = 1";
        
        $result = RunQuery($sql);
        $row = CRM_mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }

    /**
     * Contar mensagens pendentes de um ministério
     */
    public static function contarPendentes($ministerioId)
    {
        $ministerioId = (int)$ministerioId;
        $sql = "SELECT COUNT(*) as total 
                FROM ministerio_mensagens 
                WHERE ministerio_id = $ministerioId 
                AND status = 'pendente'";
        
        $result = RunQuery($sql);
        $row = CRM_mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
}