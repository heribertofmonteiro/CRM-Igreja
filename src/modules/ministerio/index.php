<?php

/**
 * Ponto de entrada principal do Módulo Ministério & Comunicação
 * 
 * Este arquivo serve como router principal para o módulo,
 * processando todas as requisições e direcionando para os controllers apropriados.
 */

// Incluir sistema de rotas
require_once __DIR__ . '/routes.php';

// Verificar se o módulo está instalado
function isModuleInstalled() {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
        'heriberto',
        '0631'
    );
    
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'ministerios'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Middleware de instalação
if (!isModuleInstalled()) {
    // Redirecionar para instalação ou mostrar página de erro
    header('Location: install.php');
    exit;
}

// Processar a requisição
processRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
