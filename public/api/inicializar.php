<?php

declare(strict_types=1);

use App\Core\Entorno;

define('BASE_PATH', dirname(__DIR__, 2));
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

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($configuracion['app']['session_name']);
    session_start();
}
