<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        $viewFile = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';

        if (!is_file($viewFile) || !is_file($layoutFile)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}
