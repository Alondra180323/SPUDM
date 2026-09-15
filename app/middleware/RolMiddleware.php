<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Autenticacion;

final class RolMiddleware
{
    public function __construct(private readonly array $roles) {}

    public function manejar(): void
    {
        if (!Autenticacion::estaAutenticado() || !in_array(Autenticacion::rol(), $this->roles, true)) {
            http_response_code(403);
            require APP_PATH . '/views/errores/403.php';
            exit;
        }
    }
}
