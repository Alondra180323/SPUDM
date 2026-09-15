<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Autenticacion;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Services\SolicitudService;

final class SolicitudController extends BaseController
{
    public function __construct(private readonly SolicitudService $servicio = new SolicitudService()) {}

    public function ver(): void
    {
        $usuario = Autenticacion::usuario() ?? [];

        $this->vista('solicitudes.ver', [
            'title' => 'Ver solicitudes',
            'solicitudes' => $this->servicio->solicitudesRecientes($usuario),
            'success' => $_SESSION['_flash_success'] ?? null,
        ]);

        unset($_SESSION['_flash_success']);
    }

    public function registrar(): void
    {
        $this->vista('solicitudes.registrar', [
            'title' => 'Registro de Solicitud',
            'trabajadores' => $this->servicio->trabajadoresActivos(),
            'errores' => $_SESSION['_flash_solicitud_errors'] ?? [],
            'anterior' => $_SESSION['_flash_solicitud_old'] ?? [],
        ]);

        unset($_SESSION['_flash_solicitud_errors'], $_SESSION['_flash_solicitud_old']);
    }

    public function aceptar(): void
    {
        $this->vista('solicitudes.aceptar', [
            'title' => 'Aceptar solicitudes',
            'solicitudes' => $this->servicio->pendientesParaAceptar(),
        ]);
    }

    public function guardar(): void
    {
        if (!Csrf::verificar($_POST['_token'] ?? null)) {
            $_SESSION['_flash_solicitud_errors'] = [
                'general' => 'La sesión del formulario expiró. Intenta nuevamente.',
            ];
            $_SESSION['_flash_solicitud_old'] = $_POST;
            $this->redireccionar('/solicitudes/nueva');
        }

        $resultado = $this->servicio->registrarSolicitud($_POST, Autenticacion::usuario() ?? []);

        if (!$resultado['correcto']) {
            $_SESSION['_flash_solicitud_errors'] = $resultado['errores'];
            $_SESSION['_flash_solicitud_old'] = $resultado['anterior'];
            $this->redireccionar('/solicitudes/nueva');
        }

        $_SESSION['_flash_success'] = 'Solicitud registrada correctamente. Folio: ' . $resultado['folio'];
        $this->redireccionar('/solicitudes');
    }
}
