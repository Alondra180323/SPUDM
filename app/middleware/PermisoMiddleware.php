<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Autenticacion;

final class PermisoMiddleware
{
    public function __construct(private readonly string $permiso) {}

    public function manejar(): void
    {
        if (!Autenticacion::estaAutenticado() || !Autenticacion::puede($this->permiso)) {
            http_response_code(403);
            require APP_PATH . '/views/errores/403.php';
            exit;
        }
    }
}
