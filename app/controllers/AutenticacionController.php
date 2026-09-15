<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Autenticacion;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Services\AutenticacionService;

final class AutenticacionController extends BaseController
{
    public function mostrarLogin(): void
    {
        $this->vista('autenticacion.login', [
            'title' => 'Iniciar sesión',
            'error' => $_SESSION['_flash_error'] ?? null,
            'usuarioAnterior' => $_SESSION['_flash_user'] ?? '',
        ], 'autenticacion');

        unset($_SESSION['_flash_error'], $_SESSION['_flash_user']);
    }

    public function autenticar(): void
    {
        if (!Csrf::verificar($_POST['_token'] ?? null)) {
            $_SESSION['_flash_error'] = 'La sesión del formulario expiró. Intenta nuevamente.';
            $this->redireccionar('/login');
        }

        $usuario = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($usuario === '' || $password === '') {
            $_SESSION['_flash_error'] = 'Ingresa usuario y contraseña.';
            $_SESSION['_flash_user'] = $usuario;
            $this->redireccionar('/login');
        }

        $resultado = (new AutenticacionService())->iniciarSesion($usuario, $password);
        if (!$resultado['correcto']) {
            $_SESSION['_flash_error'] = $resultado['mensaje'];
            $_SESSION['_flash_user'] = $usuario;
            $this->redireccionar('/login');
        }

        $this->redireccionar('/dashboard');
    }

    public function salir(): void
    {
        if (!Csrf::verificar($_POST['_token'] ?? null)) {
            http_response_code(419);
            exit('Solicitud expirada.');
        }

        Autenticacion::cerrarSesion();
        header('Location: ' . url('/login'));
        exit;
    }
}
