<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Autenticacion;
use App\Core\BaseController;
use App\Services\SolicitudService;

final class InicioController extends BaseController
{
    public function inicio(): void
    {
        $rol = Autenticacion::rol();
        $vistas = [
            'administrador' => 'inicio.administrador.index',
            'supervisor' => 'inicio.supervisor.index',
            'laborales' => 'inicio.laborales.index',
            'programacion' => 'inicio.programacion.index',
        ];

        $vista = $vistas[$rol] ?? null;
        if (!$vista) {
            http_response_code(403);
            require APP_PATH . '/views/errores/403.php';
            return;
        }

        $conteos = (new SolicitudService())->conteosInicio(Autenticacion::usuario() ?? []);

        $this->vista($vista, [
            'title' => 'Panel principal',
            'pageTitle' => 'Centro de control SPUDM',
            'conteos' => $conteos,
        ]);
    }
}
