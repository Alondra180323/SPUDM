<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Middleware\RoleMiddleware;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[$method][$this->normalize($path)] = compact('handler', 'middleware');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalize(parse_url($uri, PHP_URL_PATH) ?: '/');
        $basePath = $this->normalize((string) parse_url(app_config('app.url'), PHP_URL_PATH));

        if ($basePath !== '/' && str_starts_with($path, $basePath)) {
            $path = $this->normalize(substr($path, strlen($basePath)) ?: '/');
        }

        $route = $this->routes[$method][$path] ?? null;
        if (!$route) {
            http_response_code(404);
            require APP_PATH . '/views/errors/404.php';
            return;
        }

        foreach ($route['middleware'] as $middleware) {
            $this->runMiddleware($middleware);
        }

        [$controllerClass, $action] = $route['handler'];
        $controller = new $controllerClass();
        $controller->{$action}();
    }

    private function runMiddleware(string $middleware): void
    {
        if ($middleware === 'auth') {
            (new AuthMiddleware())->handle();
            return;
        }

        if ($middleware === 'guest') {
            (new GuestMiddleware())->handle();
            return;
        }

        if (str_starts_with($middleware, 'role:')) {
            $roles = array_filter(array_map('trim', explode(',', substr($middleware, 5))));
            (new RoleMiddleware($roles))->handle();
            return;
        }

        if (str_starts_with($middleware, 'permission:')) {
            (new PermissionMiddleware(trim(substr($middleware, 11))))->handle();
        }
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
