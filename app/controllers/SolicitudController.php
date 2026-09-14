<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\SolicitudService;

final class SolicitudController extends Controller
{
    public function __construct(private readonly SolicitudService $service = new SolicitudService()) {}

    public function index(): void
    {
        $user = Auth::user() ?? [];
        $this->view('solicitudes.index', [
            'title' => 'Ver solicitudes',
            'solicitudes' => $this->service->recent($user),
            'success' => $_SESSION['_flash_success'] ?? null,
        ]);
        unset($_SESSION['_flash_success']);
    }

    public function create(): void
    {
        $this->view('solicitudes.create', [
            'title' => 'Registro de Solicitud',
            'trabajadores' => $this->service->activeWorkers(),
            'errors' => $_SESSION['_flash_solicitud_errors'] ?? [],
            'old' => $_SESSION['_flash_solicitud_old'] ?? [],
        ]);
        unset($_SESSION['_flash_solicitud_errors'], $_SESSION['_flash_solicitud_old']);
    }

    public function acceptance(): void
    {
        $this->view('solicitudes.acceptance', [
            'title' => 'Aceptar solicitudes',
            'solicitudes' => $this->service->pendingForAcceptance(),
        ]);
    }

    public function store(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? null)) {
            $_SESSION['_flash_solicitud_errors'] = [
                'general' => 'La sesión del formulario expiró. Intenta nuevamente.',
            ];
            $_SESSION['_flash_solicitud_old'] = $_POST;
            $this->redirect('/solicitudes/nueva');
        }

        $result = $this->service->create($_POST, Auth::user() ?? []);
        if (!$result['ok']) {
            $_SESSION['_flash_solicitud_errors'] = $result['errors'];
            $_SESSION['_flash_solicitud_old'] = $result['old'];
            $this->redirect('/solicitudes/nueva');
        }

        $_SESSION['_flash_success'] = 'Solicitud registrada correctamente. Folio: ' . $result['folio'];
        $this->redirect('/solicitudes');
    }
}
