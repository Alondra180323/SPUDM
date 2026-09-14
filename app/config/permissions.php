<?php

declare(strict_types=1);

return [
    'administrador' => ['*'],
    'supervisor' => [
        'dashboard.ver',
        'solicitudes.ver',
        'solicitudes.crear',
        'solicitudes.editar',
    ],
    'laborales' => [
        'dashboard.ver',
        'solicitudes.ver',
        'solicitudes.validar',
        'usuarios.ver',
    ],
    'programacion' => [
        'dashboard.ver',
        'solicitudes.ver',
        'programacion.ver',
        'programacion.crear',
        'programacion.editar',
        'vehiculos.ver',
    ],
];
