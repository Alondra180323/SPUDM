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
    'users' => [
        // Ajusta ACTIVE_USER_STATUS_ID en .env si el ID del estatus Activo es diferente.
        'active_status_id' => (int) (getenv('ACTIVE_USER_STATUS_ID') ?: 1),
    ],
    'solicitudes' => [
        // IDs reales de tu catálogo de estatus de solicitud.
        'status' => [
            'pendiente' => (int) (getenv('SOLICITUD_STATUS_PENDIENTE_ID') ?: 1),
            'en_espera' => (int) (getenv('SOLICITUD_STATUS_ESPERA_ID') ?: 2),
            'programada' => (int) (getenv('SOLICITUD_STATUS_PROGRAMADA_ID') ?: 3),
            'realizada' => (int) (getenv('SOLICITUD_STATUS_REALIZADA_ID') ?: 4),
            'cancelada' => (int) (getenv('SOLICITUD_STATUS_CANCELADA_ID') ?: 5),
            'vencida' => (int) (getenv('SOLICITUD_STATUS_VENCIDA_ID') ?: 6),
        ],
        // ID_TIPO_PROGRAMA: cambia estos valores si Único/Fijo usan otros IDs.
        'tipo_programa' => [
            'unico' => (int) (getenv('SOLICITUD_TIPO_PROGRAMA_UNICO_ID') ?: 1),
            'fijo' => (int) (getenv('SOLICITUD_TIPO_PROGRAMA_FIJO_ID') ?: 2),
        ],
    ],
];
