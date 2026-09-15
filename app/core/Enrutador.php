<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AutenticacionMiddleware;
use App\Middleware\InvitadoMiddleware;
use App\Middleware\PermisoMiddleware;
use App\Middleware\RolMiddleware;

final class Enrutador
{
    private array $rutas = [];

    public function get(string $ruta, array $manejador, array $middlewares = []): void
    {
        $this->agregar('GET', $ruta, $manejador, $middlewares);
    }

    public function post(string $ruta, array $manejador, array $middlewares = []): void
    {
        $this->agregar('POST', $ruta, $manejador, $middlewares);
    }

    private function agregar(string $metodo, string $ruta, array $manejador, array $middlewares): void
    {
        $this->rutas[$metodo][$this->normalizar($ruta)] = compact('manejador', 'middlewares');
    }

    public function despachar(string $metodo, string $uri): void
    {
        $ruta = $this->normalizar(parse_url($uri, PHP_URL_PATH) ?: '/');
        $rutaBase = $this->normalizar((string) parse_url(configuracion('app.url'), PHP_URL_PATH));

        if ($rutaBase !== '/' && str_starts_with($ruta, $rutaBase)) {
            $ruta = $this->normalizar(substr($ruta, strlen($rutaBase)) ?: '/');
        }

        $definicion = $this->rutas[$metodo][$ruta] ?? null;
        if (!$definicion) {
            http_response_code(404);
            require APP_PATH . '/views/errores/404.php';
            return;
        }

        foreach ($definicion['middlewares'] as $middleware) {
            $this->ejecutarMiddleware($middleware);
        }

        [$claseControlador, $accion] = $definicion['manejador'];
        $controlador = new $claseControlador();
        $controlador->{$accion}();
    }

    private function ejecutarMiddleware(string $middleware): void
    {
        if ($middleware === 'auth') {
            (new AutenticacionMiddleware())->manejar();
            return;
        }

        if ($middleware === 'guest') {
            (new InvitadoMiddleware())->manejar();
            return;
        }

        if (str_starts_with($middleware, 'role:')) {
            $roles = array_filter(array_map('trim', explode(',', substr($middleware, 5))));
            (new RolMiddleware($roles))->manejar();
            return;
        }

        if (str_starts_with($middleware, 'permission:')) {
            $permiso = trim(substr($middleware, 11));
            (new PermisoMiddleware($permiso))->manejar();
        }
    }

    private function normalizar(string $ruta): string
    {
        $ruta = '/' . trim($ruta, '/');
        return $ruta === '//' ? '/' : $ruta;
    }
}
