<?php
/**
 * Configuração do Módulo Ministério
 */
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











