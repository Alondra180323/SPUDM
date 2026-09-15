<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Autenticacion;

final class InvitadoMiddleware
{
    public function manejar(): void
    {
        if (Autenticacion::estaAutenticado()) {
            header('Location: ' . url('/dashboard'));
            exit;
        }
    }
}
