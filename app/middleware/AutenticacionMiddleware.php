<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Autenticacion;

final class AutenticacionMiddleware
{
    public function manejar(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            header('Location: ' . url('/login'));
            exit;
        }
    }
}
