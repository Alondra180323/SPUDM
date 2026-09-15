<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected function vista(string $vista, array $datos = [], string $plantilla = 'app'): void
    {
        $archivoVista = APP_PATH . '/views/' . str_replace('.', '/', $vista) . '.php';
        $archivoPlantilla = APP_PATH . '/views/plantillas/' . $plantilla . '.php';

        if (!is_file($archivoVista) || !is_file($archivoPlantilla)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($datos, EXTR_SKIP);
        ob_start();
        require $archivoVista;
        $contenido = ob_get_clean();
        require $archivoPlantilla;
    }

    protected function redireccionar(string $ruta): never
    {
        header('Location: ' . url($ruta));
        exit;
    }

    protected function json(array $datos, int $codigo = 200): never
    {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
