<?php
/**
 * Script de cron para processar fila de mensagens
 * Deve ser executado a cada 5 minutos
 * 
 * Uso: php /var/www/html/modules/ministerio/cron.php
 * Ou configure no crontab: */5 * * * * php /var/www/html/modules/ministerio/cron.php
 */

// Definir que está rodando via CLI
if (php_sapi_name() !== 'cli') {
    die("Este script deve ser executado via linha de comando\n");
}

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Definir caminho base
$basePath = dirname(dirname(dirname(__DIR__)));
chdir($basePath);

// Incluir arquivos necessários
require_once 'Include/Config.php';
require_once 'Include/Functions.php';
require_once 'modules/ministerio/QueueManager.php';

// Configurar limite de tempo e memória
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '256M');

// Função para log
function logCron($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}\n";
    
    // Log para arquivo
    $logFile = dirname(__FILE__) . '/logs/cron.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    
    // Log para console
    echo $logMessage;
}

// Função para criar diretório de logs se não existir
function ensureLogDirectory() {
    $logDir = dirname(__FILE__) . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
}

// Função para enviar notificação de erro
function notifyError($message) {
    $adminEmail = SystemConfig::getValue('sChurchEmail');
    if (!empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $subject = '[ChurchCRM] Erro no processamento de mensagens';
        $body = "Ocorreu um erro no processamento automático de mensagens:\n\n{$message}\n\nData: " . date('Y-m-d H:i:s');
        
        mail($adminEmail, $subject, $body);
    }
}

try {
    logCron('Iniciando processamento da fila de mensagens...');
    
    // Criar diretório de logs
    ensureLogDirectory();
    
    // Verificar se o módulo está ativo
    if (!SystemConfig::getBooleanValue('bMinisterioEnabled')) {
        logCron('Módulo Ministério desativado. Abortando.');
        exit(0);
    }
    
    // Criar instância do QueueManager
    $queueManager = new QueueManager();
    
    // Obter estatísticas antes do processamento
    $statsBefore = $queueManager->getQueueStats();
    logCron("Estatísticas antes do processamento: " . json_encode($statsBefore));
    
    // Processar fila
    $processed = $queueManager->processQueue();
    
    // Obter estatísticas após o processamento
    $statsAfter = $queueManager->getQueueStats();
    logCron("Estatísticas após o processamento: " . json_encode($statsAfter));
    
    // Calcular mensagens processadas com sucesso
    $successCount = $statsBefore['pendente'] - $statsAfter['pendente'];
    if ($successCount < 0) $successCount = 0;
    
    logCron("Processamento concluído. {$processed} mensagens processadas, {$successCount} enviadas com sucesso.");
    
    // Verificar se há muitas mensagens falhadas
    if ($statsAfter['falhou'] > 10) {
        $warning = "Atenção: {$statsAfter['falhou']} mensagens falhadas acumuladas.";
        logCron($warning);
        notifyError($warning);
    }
    
    // Limpar logs antigos (manter últimos 30 dias)
    $logFile = dirname(__FILE__) . '/logs/cron.log';
    if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
        logCron('Arquivo de log muito grande. Rotacionando...');
        rename($logFile, $logFile . '.' . date('Y-m-d-H-i-s'));
    }
    
    exit(0);
    
} catch (Exception $e) {
    $error = "Erro fatal no processamento: " . $e->getMessage();
    logCron($error);
    notifyError($error);
    exit(1);
}