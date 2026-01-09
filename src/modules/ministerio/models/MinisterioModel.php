<?php

/**
 * Model para Ministério
 * Gerencia operações CRUD e consultas relacionadas a ministérios
 */
class MinisterioModel
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
     * Listar todos os ministérios
     */
    public static function list()
    {
        $pdo = self::initConnection();
        $stmt = $pdo->query("
            SELECT m.*, 
                   p1.name as lider_nome,
                   p2.name as coordenador_nome
            FROM ministerios m
            LEFT JOIN users p1 ON m.lider_id = p1.id
            LEFT JOIN users p2 ON m.coordenador_id = p2.id
            WHERE m.ativo = 1
            ORDER BY m.nome
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar ministério por ID
     */
    public static function buscarPorId($id)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            SELECT m.*, 
                   p1.name as lider_nome,
                   p2.name as coordenador_nome
            FROM ministerios m
            LEFT JOIN users p1 ON m.lider_id = p1.id
            LEFT JOIN users p2 ON m.coordenador_id = p2.id
            WHERE m.id = :id AND m.ativo = 1
        ");
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        
        return $row; // Retornar array em vez de objeto
    }
    
    /**
     * Criar novo ministério (alias)
     */
    public static function create($dados)
    {
        return self::criar($dados);
    }
    
    /**
     * Atualizar ministério (alias)
     */
    public static function update($id, $dados)
    {
        return self::atualizar($id, $dados);
    }
    
    /**
     * Excluir ministério (alias)
     */
    public static function delete($id)
    {
        return self::excluir($id);
    }
    
    /**
     * Buscar por ID (alias)
     */
    public static function findById($id)
    {
        return self::buscarPorId($id);
    }
    
    /**
     * Adicionar membro ao ministério
     */
    public static function addMember($ministerioId, $membroId, $funcao = 'Membro')
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            INSERT INTO ministerio_membros (ministerio_id, membro_id, funcao, data_entrada, ativo, criado_em)
            VALUES (:ministerio_id, :membro_id, :funcao, CURDATE(), 1, NOW())
        ");
        
        return $stmt->execute([
            ':ministerio_id' => $ministerioId,
            ':membro_id' => $membroId,
            ':funcao' => $funcao
        ]);
    }
    
    /**
     * Remover membro do ministério
     */
    public static function removeMember($ministerioId, $membroId)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            UPDATE ministerio_membros 
            SET ativo = 0, data_saida = CURDATE()
            WHERE ministerio_id = :ministerio_id AND membro_id = :membro_id
        ");
        
        return $stmt->execute([
            ':ministerio_id' => $ministerioId,
            ':membro_id' => $membroId
        ]);
    }
    
    /**
     * Listar membros de um ministério
     */
    public static function listMembers($ministerioId)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            SELECT mm.*, u.name as nome, u.email
            FROM ministerio_membros mm
            LEFT JOIN users u ON mm.membro_id = u.id
            WHERE mm.ministerio_id = :ministerio_id AND mm.ativo = 1
            ORDER BY u.name
        ");
        $stmt->execute([':ministerio_id' => $ministerioId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Listar membros (alias)
     */
    public static function listarMembros($ministerioId)
    {
        return self::listMembers($ministerioId);
    }
    
    /**
     * Criar novo ministério
     */
    public static function criar($dados)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            INSERT INTO ministerios (nome, descricao, lider_id, coordenador_id, ativo, criado_em)
            VALUES (:nome, :descricao, :lider_id, :coordenador_id, :ativo, NOW())
        ");
        
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'] ?? null,
            ':lider_id' => $dados['lider_id'],
            ':coordenador_id' => $dados['coordenador_id'] ?? null,
            ':ativo' => $dados['ativo'] ?? 1
        ]);
        
        return $pdo->lastInsertId();
    }
    
    /**
     * Atualizar ministério
     */
    public static function atualizar($id, $dados)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            UPDATE ministerios 
            SET nome = :nome, 
                descricao = :descricao, 
                coordenador_id = :coordenador_id,
                ativo = :ativo,
                atualizado_em = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'] ?? null,
            ':coordenador_id' => $dados['coordenador_id'] ?? null,
            ':ativo' => $dados['ativo'] ?? 1
        ]);
    }
    
    /**
     * Excluir ministério (soft delete)
     */
    public static function excluir($id)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            UPDATE ministerios 
            SET ativo = 0, atualizado_em = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Contar total de ministérios ativos
     */
    public static function contarTotal()
    {
        $pdo = self::initConnection();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM ministerios WHERE ativo = 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
    
    /**
     * Remover membro do ministério
     */
    public static function removerMembro($ministerioId, $membroId)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            UPDATE ministerio_membros 
            SET ativo = 0, data_saida = CURDATE()
            WHERE ministerio_id = :ministerio_id AND membro_id = :membro_id
        ");
        
        return $stmt->execute([
            ':ministerio_id' => $ministerioId,
            ':membro_id' => $membroId
        ]);
    }
    
    /**
     * Contar total de membros em todos os ministérios
     */
    public static function contarTotalMembros()
    {
        $pdo = self::initConnection();
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM ministerio_membros 
            WHERE ativo = 1
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
    
    /**
     * Listar pessoas que não são membros de um ministério específico
     */
    public static function listarPessoasNaoMembros($ministerioId)
    {
        $pdo = self::initConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.FullName as nome
            FROM users u
            WHERE u.id NOT IN (
                SELECT membro_id 
                FROM ministerio_membros 
                WHERE ministerio_id = :ministerio_id AND ativo = 1
            )
            ORDER BY u.FullName
        ");
        $stmt->execute([':ministerio_id' => $ministerioId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
