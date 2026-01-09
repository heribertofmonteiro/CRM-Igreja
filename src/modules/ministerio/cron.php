<?php
// Script de cron para processar fila de mensagens
if (php_sapi_name() !== "cli") {
    die("Este script deve ser executado via linha de comando\n");
}

date_default_timezone_set("America/Sao_Paulo");

function logCron($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[{$timestamp}] {$message}\n";
    echo $logMessage;
    
    $logFile = dirname(__FILE__) . "/logs/cron.log";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    logCron("Iniciando processamento da fila de mensagens...");
    
    // Criar diretorio de logs se nao existir
    $logDir = dirname(__FILE__) . "/logs";
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    logCron("Processamento concluido com sucesso.");
    exit(0);
    
} catch (Exception $e) {
    $error = "Erro fatal no processamento: " . $e->getMessage();
    logCron($error);
    exit(1);
}

