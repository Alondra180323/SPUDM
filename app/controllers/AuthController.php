<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth.login', [
            'title' => 'Iniciar sesión',
            'error' => $_SESSION['_flash_error'] ?? null,
            'oldUser' => $_SESSION['_flash_user'] ?? '',
        ], 'auth');

        unset($_SESSION['_flash_error'], $_SESSION['_flash_user']);
    }

    public function authenticate(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? null)) {
            $_SESSION['_flash_error'] = 'La sesión del formulario expiró. Intenta nuevamente.';
            $this->redirect('/login');
        }

        $username = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $_SESSION['_flash_error'] = 'Ingresa usuario y contraseña.';
            $_SESSION['_flash_user'] = $username;
            $this->redirect('/login');
        }

        $result = (new AuthService())->attempt($username, $password);
        if (!$result['ok']) {
            $_SESSION['_flash_error'] = $result['message'];
            $_SESSION['_flash_user'] = $username;
            $this->redirect('/login');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? null)) {
            http_response_code(419);
            exit('Solicitud expirada.');
        }

        Auth::logout();
        header('Location: ' . url('/login'));
        exit;
    }
}
