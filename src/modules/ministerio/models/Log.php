<?php
/**
 * Model: Log de Auditoria
 */
require_once __DIR__ . '/../../../Include/Functions.php';

class MinisterioLogModel
{
    /**
     * Registrar ação no log
     */
    public static function registrar($usuarioId, $acao, $tabela, $registroId = null, $dadosAntigos = null, $dadosNovos = null)
    {
        $usuarioId = $usuarioId ? (int)$usuarioId : 'NULL';
        $acao = mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $acao);
        $tabela = mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $tabela);
        $registroId = $registroId ? (int)$registroId : 'NULL';
        
        $dadosAntigosJson = $dadosAntigos ? "'" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], json_encode($dadosAntigos)) . "'" : 'NULL';
        $dadosNovosJson = $dadosNovos ? "'" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], json_encode($dadosNovos)) . "'" : 'NULL';
        
        $ipOrigem = $_SERVER['REMOTE_ADDR'] ?? 'NULL';
        $ipOrigem = $ipOrigem !== 'NULL' ? "'" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $ipOrigem) . "'" : 'NULL';
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'NULL';
        $userAgent = $userAgent !== 'NULL' ? "'" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $userAgent) . "'" : 'NULL';
        
        $sql = "INSERT INTO ministerio_logs 
                (usuario_id, acao, tabela, registro_id, dados_antigos, dados_novos, ip_origem, user_agent) 
                VALUES ($usuarioId, '$acao', '$tabela', $registroId, $dadosAntigosJson, $dadosNovosJson, $ipOrigem, $userAgent)";
        
        RunQuery($sql);
        return mysqli_insert_id($GLOBALS['cnInfoCentral']);
    }
    
    /**
     * Listar logs
     */
    public static function listar($filtros = [])
    {
        $sql = "SELECT l.*, 
                p.per_FirstName, p.per_LastName
                FROM ministerio_logs l
                LEFT JOIN person_per p ON l.usuario_id = p.per_ID
                WHERE 1=1";
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND l.usuario_id = " . (int)$filtros['usuario_id'];
        }
        
        if (!empty($filtros['acao'])) {
            $acao = mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['acao']);
            $sql .= " AND l.acao = '$acao'";
        }
        
        if (!empty($filtros['tabela'])) {
            $tabela = mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['tabela']);
            $sql .= " AND l.tabela = '$tabela'";
        }
        
        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND l.criado_em >= '" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['data_inicio']) . "'";
        }
        
        if (!empty($filtros['data_fim'])) {
            $sql .= " AND l.criado_em <= '" . mysqli_real_escape_string($GLOBALS['cnInfoCentral'], $filtros['data_fim']) . "'";
        }
        
        $sql .= " ORDER BY l.criado_em DESC LIMIT 1000";
        
        $result = RunQuery($sql);
        $logs = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
        
        return $logs;
    }
}


