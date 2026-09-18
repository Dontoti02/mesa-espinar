<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Mesa de Partes Virtual',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost/mesa-espinar', '/'),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Lima',
    'key' => $_ENV['APP_KEY'] ?? 'secret_key_default',

    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
        'cookie' => $_ENV['SESSION_COOKIE_NAME'] ?? 'mesapartes_session',
    ],

    'uploads' => [
        'max_size_mb' => (int)($_ENV['MAX_FILE_SIZE_MB'] ?? 25),
        'allowed_extensions' => explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png'),
        'documents_path' => dirname(__DIR__) . '/storage/documents',
        'public_uploads_path' => dirname(__DIR__) . '/public/uploads',
    ],

    'security' => [
        'max_login_attempts' => (int)($_ENV['LOGIN_MAX_ATTEMPTS'] ?? 5),
        'lockout_minutes' => (int)($_ENV['LOGIN_LOCKOUT_MINUTES'] ?? 15),
    ]
];
