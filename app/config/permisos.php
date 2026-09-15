<?php

declare(strict_types=1);

return [
    'administrador' => ['*'],
    'supervisor' => [
        'dashboard.ver',
        'solicitudes.ver',
        'solicitudes.crear',
        'solicitudes.editar',
        'solicitudes.validar',
        'catalogos.ver',
    ],
    'laborales' => [
        'dashboard.ver',
        'solicitudes.ver',
        'solicitudes.validar',
        'usuarios.ver',
        'catalogos.ver',
    ],
    'programacion' => [
        'dashboard.ver',
        'solicitudes.ver',
        'programacion.ver',
        'programacion.crear',
        'programacion.editar',
        'vehiculos.ver',
        'catalogos.ver',
    ],
];
