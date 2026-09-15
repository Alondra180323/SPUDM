<?php

declare(strict_types=1);

namespace App\Core;

final class ApiAutenticacion
{
    public static function requerirSesion(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            ManejadorApiBase::responder([
                'correcto' => false,
                'mensaje' => 'Sesión no válida.',
            ], 401);
        }
    }

    public static function requerirPermiso(string $permiso): void
    {
        self::requerirSesion();

        if (!Autenticacion::puede($permiso)) {
            ManejadorApiBase::responder([
                'correcto' => false,
                'mensaje' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }
    }
}
