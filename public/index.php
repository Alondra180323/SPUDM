<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SolicitudController;
use App\Core\Env;
use App\Core\Router;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require APP_PATH . '/core/Env.php';
Env::load(BASE_PATH . '/.env');

spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $relative = substr($class, 4);
    $segments = explode('\\', $relative);
    $filename = array_pop($segments) . '.php';
    $directories = array_map('lcfirst', $segments);
    $file = APP_PATH . '/' . implode('/', $directories) . '/' . $filename;

    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/helpers/helpers.php';

$config = app_config();
date_default_timezone_set($config['app']['timezone']);

session_name($config['app']['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$router = new Router();
$router->get('/', [AuthController::class, 'login'], ['guest']);
$router->get('/login', [AuthController::class, 'login'], ['guest']);
$router->post('/login', [AuthController::class, 'authenticate'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

$router->get('/solicitudes', [SolicitudController::class, 'index'], ['auth', 'permission:solicitudes.ver']);
$router->get('/solicitudes/nueva', [SolicitudController::class, 'create'], ['auth', 'permission:solicitudes.crear']);
$router->get('/solicitudes/aceptar', [SolicitudController::class, 'acceptance'], ['auth', 'permission:solicitudes.validar']);
$router->post('/solicitudes', [SolicitudController::class, 'store'], ['auth', 'permission:solicitudes.crear']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
