<?php
// Configuração do ChurchCRM para uso sem Docker
// Este arquivo sobrescreve as configurações padrão para ambiente local

return [
    // Configurações de Banco de Dados
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'database' => $_ENV['DB_DATABASE'] ?? 'churchcrm',
        'username' => $_ENV['DB_USER'] ?? 'churchcrm',
        'password' => $_ENV['DB_PASSWORD'] ?? 'churchcrm123',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

    // Configurações da Aplicação
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
        'timezone' => 'America/Sao_Paulo',
        'locale' => 'pt_BR',
    ],

    // Configurações de E-mail
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'port' => $_ENV['MAIL_PORT'] ?? 1025,
        'username' => $_ENV['MAIL_USERNAME'] ?? null,
        'password' => $_ENV['MAIL_PASSWORD'] ?? null,
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? null,
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@churchcrm.local',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'ChurchCRM',
        ],
    ],

    // Configurações de Upload
    'upload' => [
        'dir' => $_ENV['UPLOAD_DIR'] ?? __DIR__ . '/src/Uploads',
        'max_size' => $_ENV['MAX_UPLOAD_SIZE'] ?? '10M',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ],

    // Configurações de Log
    'log' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'path' => $_ENV['LOG_PATH'] ?? __DIR__ . '/src/logs',
        'max_files' => 30,
        'max_size' => '10M',
    ],

    // Configurações de Sessão
    'session' => [
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120,
        'path' => $_ENV['SESSION_PATH'] ?? __DIR__ . '/src/sessions',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    // Configurações de Cache
    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'path' => $_ENV['CACHE_PATH'] ?? __DIR__ . '/src/cache',
        'prefix' => 'churchcrm_',
    ],

    // Configurações de Segurança
    'security' => [
        'jwt_secret' => $_ENV['JWT_SECRET'] ?? 'change_this_jwt_secret_key',
        'encryption_key' => $_ENV['ENCRYPTION_KEY'] ?? 'change_this_encryption_key',
        'password_min_length' => 8,
        'session_timeout' => 3600, // 1 hora
    ],

    // Configurações de API
    'api' => [
        'rate_limit' => $_ENV['API_RATE_LIMIT'] ?? 100,
        'timeout' => $_ENV['API_TIMEOUT'] ?? 30,
        'cors' => [
            'allowed_origins' => ['http://localhost:8080', 'http://localhost:3000'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        ],
    ],

    // Configurações de Desenvolvimento
    'development' => [
        'show_errors' => true,
        'log_queries' => true,
        'profiler' => true,
        'debug_bar' => true,
    ],
];
