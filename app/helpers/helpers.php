<?php

declare(strict_types=1);

use App\Core\Autenticacion;
use App\Core\Csrf;

function configuracion(?string $clave = null): mixed
{
    static $configuracion;
    $configuracion ??= require APP_PATH . '/config/config.php';

    if ($clave === null) {
        return $configuracion;
    }

    $segmentos = explode('.', $clave);
    $valor = $configuracion;

    foreach ($segmentos as $segmento) {
        if (!is_array($valor) || !array_key_exists($segmento, $valor)) {
            return null;
        }
        $valor = $valor[$segmento];
    }

    return $valor;
}

function url(string $ruta = ''): string
{
    $base = rtrim((string) configuracion('app.url'), '/');
    return $base . '/' . ltrim($ruta, '/');
}

function recurso(string $ruta): string
{
    return url('assets/' . ltrim($ruta, '/'));
}

function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function campo_csrf(): string
{
    return '<input type="hidden" name="_token" value="' . escapar(Csrf::token()) . '">';
}

function usuario_autenticado(): ?array
{
    return Autenticacion::usuario();
}

function tiene_permiso(string $permiso): bool
{
    return Autenticacion::puede($permiso);
}

/* Alias de compatibilidad para vistas existentes. */
function app_config(?string $clave = null): mixed { return configuracion($clave); }
function asset(string $ruta): string { return recurso($ruta); }
function e(mixed $valor): string { return escapar($valor); }
function csrf_field(): string { return campo_csrf(); }
function auth_user(): ?array { return usuario_autenticado(); }
function can(string $permiso): bool { return tiene_permiso($permiso); }
