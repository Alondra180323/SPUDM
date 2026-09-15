<?php

declare(strict_types=1);

use App\Controllers\AutenticacionController;
use App\Controllers\CatalogoController;
use App\Controllers\InicioController;
use App\Controllers\SolicitudController;
use App\Core\Entorno;
use App\Core\Enrutador;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require APP_PATH . '/core/Entorno.php';
Entorno::cargar(BASE_PATH . '/.env');

spl_autoload_register(function (string $clase): void {
    if (!str_starts_with($clase, 'App\\')) {
        return;
    }

    $relativa = substr($clase, 4);
    $segmentos = explode('\\', $relativa);
    $archivo = array_pop($segmentos) . '.php';
    $directorios = array_map('lcfirst', $segmentos);
    $ruta = APP_PATH . '/' . implode('/', $directorios) . '/' . $archivo;

    if (is_file($ruta)) {
        require $ruta;
    }
});

require APP_PATH . '/helpers/helpers.php';

$configuracion = configuracion();
date_default_timezone_set($configuracion['app']['timezone']);

session_name($configuracion['app']['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$enrutador = new Enrutador();

$enrutador->get('/', [AutenticacionController::class, 'mostrarLogin'], ['guest']);
$enrutador->get('/login', [AutenticacionController::class, 'mostrarLogin'], ['guest']);
$enrutador->post('/login', [AutenticacionController::class, 'autenticar'], ['guest']);
$enrutador->post('/logout', [AutenticacionController::class, 'salir'], ['auth']);

$enrutador->get('/dashboard', [InicioController::class, 'inicio'], ['auth']);

$enrutador->get('/solicitudes', [SolicitudController::class, 'ver'], ['auth', 'permission:solicitudes.ver']);
$enrutador->get('/solicitudes/nueva', [SolicitudController::class, 'registrar'], ['auth', 'permission:solicitudes.crear']);
$enrutador->get('/solicitudes/aceptar', [SolicitudController::class, 'aceptar'], ['auth', 'permission:solicitudes.validar']);
$enrutador->post('/solicitudes', [SolicitudController::class, 'guardar'], ['auth', 'permission:solicitudes.crear']);

$enrutador->get('/catalogos/trabajadores-activos', [CatalogoController::class, 'trabajadoresActivos'], ['auth', 'permission:catalogos.ver']);

$enrutador->despachar($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
