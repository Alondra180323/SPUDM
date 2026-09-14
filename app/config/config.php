<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'SPUDM',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
        'url' => rtrim(getenv('APP_URL') ?: 'http://localhost/SPUDM/public', '/'),
        'timezone' => getenv('APP_TIMEZONE') ?: 'America/Monterrey',
        'session_name' => 'SPUDM_SESSION',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'spudm',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
