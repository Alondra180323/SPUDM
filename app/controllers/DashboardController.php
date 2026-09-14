<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $role = Auth::role();
        $views = [
            'administrador' => 'dashboard.admin.index',
            'supervisor' => 'dashboard.supervisor.index',
            'laborales' => 'dashboard.laborales.index',
            'programacion' => 'dashboard.programacion.index',
        ];

        $view = $views[$role] ?? null;
        if (!$view) {
            http_response_code(403);
            require APP_PATH . '/views/errors/403.php';
            return;
        }

        $this->view($view, [
            'title' => 'Panel principal',
            'pageTitle' => 'Centro de control SPUDM',
        ]);
    }
}
