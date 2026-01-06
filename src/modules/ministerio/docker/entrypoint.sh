#!/bin/bash

# Script de entrada do container Docker
# Configura e inicia os serviços necessários

set -e

echo "Iniciando container do ChurchCRM com módulo Ministério..."

# Criar diretórios necessários
mkdir -p /var/log/supervisor
mkdir -p /var/log/ministerio
mkdir -p /var/log/apache2
mkdir -p /var/www/html/modules/ministerio/logs

# Ajustar permissões
echo "Ajustando permissões..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/modules/ministerio/logs
chmod -R 777 /var/log/ministerio

# Configurar Apache
echo "Configurando Apache..."
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Configurações específicas do módulo Ministério
    <Directory /var/www/html/modules/ministerio>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Proteger arquivos de log
    <Directory /var/www/html/modules/ministerio/logs>
        Require all denied
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Configurar PHP
echo "Configurando PHP..."
cat > /usr/local/etc/php/conf.d/ministerio.ini << EOF
; Configurações específicas do módulo Ministério
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
session.gc_maxlifetime = 86400
; Configurações de segurança
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
EOF

# Criar arquivo de configuração do módulo se não existir
if [ ! -f /var/www/html/modules/ministerio/config.json ]; then
    echo "Criando configuração do módulo..."
    cat > /var/www/html/modules/ministerio/config.json << EOF
{
    "enabled": true,
    "queue_worker_enabled": true,
    "max_retries": 3,
    "retry_delay": 300,
    "cleanup_interval": 86400,
    "log_level": "info",
    "security": {
        "encrypt_tokens": true,
        "csrf_protection": true,
        "rate_limit": 100
    },
    "providers": {
        "twilio": {
            "enabled": false,
            "timeout": 30
        },
        "zenvia": {
            "enabled": false,
            "timeout": 30
        },
        "email": {
            "enabled": true,
            "timeout": 60
        }
    }
}
EOF
fi

# Criar script de limpeza de tokens se não existir
if [ ! -f /var/www/html/modules/ministerio/cleanup-tokens.php ]; then
    echo "Criando script de limpeza de tokens..."
    cat > /var/www/html/modules/ministerio/cleanup-tokens.php << 'EOF'
<?php
/**
 * Script de limpeza de tokens RSVP expirados
 */

require_once dirname(dirname(__DIR__)) . '/Include/Config.php';
require_once dirname(__DIR__) . '/Security.php';

function logCleanup($message) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[{$timestamp}] {$message}\n";
}

try {
    logCleanup('Iniciando limpeza de tokens expirados...');
    
    // Como os tokens são validados por timestamp, não precisamos limpar do banco
    // Mas podemos limpar logs antigos
    $sql = "DELETE FROM ministerio_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)";
    $deletedLogs = RunQuery($sql);
    
    logCleanup("Logs antigos removidos: {$deletedLogs}");
    logCleanup('Limpeza concluída.');
    
} catch (Exception $e) {
    logCleanup('Erro na limpeza: ' . $e->getMessage());
}
EOF
fi

# Criar script de health check se não existir
if [ ! -f /var/www/html/modules/ministerio/health-check.php ]; then
    echo "Criando script de health check..."
    cat > /var/www/html/modules/ministerio/health-check.php << 'EOF'
<?php
/**
 * Script de verificação de integridade do sistema
 */

require_once dirname(dirname(__DIR__)) . '/Include/Config.php';
require_once dirname(__DIR__) . '/QueueManager.php';

function logHealth($message) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[{$timestamp}] {$message}\n";
}

try {
    logHealth('Iniciando verificação de integridade...');
    
    // Verificar conexão com banco
    $sql = "SELECT COUNT(*) as total FROM ministerio_mensagens_envio WHERE status = 'falhou' AND tentativas >= 3";
    $result = RunQuery($sql);
    $row = CRM_mysqli_fetch_assoc($result);
    
    if ($row['total'] > 0) {
        logHealth("ATENÇÃO: {$row['total']} mensagens falharam permanentemente.");
    }
    
    // Verificar estatísticas
    $queueManager = new QueueManager();
    $stats = $queueManager->getQueueStats();
    
    logHealth("Estatísticas da fila: " . json_encode($stats));
    
    // Verificar espaço em disco
    $freeSpace = disk_free_space('/');
    $totalSpace = disk_total_space('/');
    $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
    
    if ($usedPercent > 90) {
        logHealth("ATENÇÃO: Disco quase cheio ({$usedPercent}% usado)");
    }
    
    logHealth('Verificação concluída.');
    
} catch (Exception $e) {
    logHealth('Erro na verificação: ' . $e->getMessage());
}
EOF
fi

# Verificar se o banco está acessível
echo "Verificando conexão com banco de dados..."
php -r "
require_once '/var/www/html/Include/Config.php';
try {
    \$sql = 'SELECT 1';
    \$result = RunQuery(\$sql);
    echo 'Conexão com banco OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERRO: Não foi possível conectar ao banco: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

# Iniciar supervisor
echo "Iniciando supervisor..."
exec "$@"