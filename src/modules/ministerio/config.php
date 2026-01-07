<?php
/**
 * Configurações do Módulo Ministério & Comunicação
 * 
 * Define todas as configurações e constantes utilizadas pelo módulo
 */

// Versão do módulo
define('MINISTERIO_VERSION', '1.0.0');

// Nome do módulo
define('MINISTERIO_NAME', 'Ministério & Comunicação');

// Configurações de banco de dados
define('MINISTERIO_DB_PREFIX', 'ministerio_');

// Configurações de sistema
define('MINISTERIO_UPLOAD_PATH', __DIR__ . '/uploads/');
define('MINISTERIO_LOG_PATH', __DIR__ . '/logs/');

// Configurações de envio de mensagens
define('MINISTERIO_EMAIL_FROM', 'noreply@igreja.com');
define('MINISTERIO_EMAIL_FROM_NAME', 'Igreja CMS');
define('MINISTERIO_WHATSAPP_API_KEY', ''); // Configurar se disponível
define('MINISTERIO_SMS_API_KEY', ''); // Configurar se disponível

// Configurações de permissões padrão
define('MINISTERIO_DEFAULT_PERMISSIONS', [
    'ministerio_ver',
    'ministerio_dashboard'
]);

// Configurações de limites
define('MINISTERIO_MAX_MEMBERS_PER_MINISTRY', 100);
define('MINISTERIO_MAX_MESSAGE_RECIPIENTS', 500);
define('MINISTERIO_MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Configurações de cache
define('MINISTERIO_CACHE_ENABLED', true);
define('MINISTERIO_CACHE_TTL', 3600); // 1 hora

// Configurações de notificação
define('MINISTERIO_NOTIFY_NEW_MEMBER', true);
define('MINISTERIO_NOTIFY_MEETING_REMINDER', true);
define('MINISTERIO_NOTIFY_MESSAGE_DELIVERY', true);

// Configurações de dashboard
define('MINISTERIO_DASHBOARD_WIDGETS', [
    'estatisticas_gerais' => true,
    'grafico_membros' => true,
    'atividades_recentes' => true,
    'proximas_reunioes' => true
]);

// Configurações de validação
define('MINISTERIO_VALIDATE_REQUIRED', true);
define('MINISTERIO_VALIDATE_EMAIL', true);
define('MINISTERIO_VALIDATE_PHONE', true);

// Configurações de integração
define('MINISTERIO_INTEGRATE_CALENDAR', true);
define('MINISTERIO_INTEGRATE_NOTIFICATIONS', true);
define('MINISTERIO_INTEGRATE_REPORTS', true);

// Configurações de desenvolvimento
define('MINISTERIO_DEBUG_MODE', false);
define('MINISTERIO_LOG_QUERIES', false);
define('MINISTERIO_SHOW_ERRORS', true);

// Wrappers CRM_mysqli_*: no runtime real delegam para funções nativas
if (!function_exists('CRM_mysqli_fetch_assoc')) {
    function CRM_mysqli_fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('CRM_mysqli_insert_id')) {
    function CRM_mysqli_insert_id($connection) {
        return mysqli_insert_id($connection);
    }
}

if (!function_exists('CRM_mysqli_affected_rows')) {
    function CRM_mysqli_affected_rows($connection) {
        return mysqli_affected_rows($connection);
    }
}

if (!function_exists('CRM_mysqli_real_escape_string')) {
    function CRM_mysqli_real_escape_string($connection, $string) {
        return mysqli_real_escape_string($connection, $string);
    }
}

return [
    'module_name' => 'ministerio',
    'module_version' => '1.0.0',
    'rate_limit' => 50, // mensagens por minuto
    'channels' => ['email', 'whatsapp', 'sms', 'interno'],
    'rsvp_token_length' => 64,
    'reminder_hours_before' => 24,
];











