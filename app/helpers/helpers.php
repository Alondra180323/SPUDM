<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;

function app_config(?string $key = null): mixed
{
    static $config;
    $config ??= require APP_PATH . '/config/config.php';

    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return null;
        }
        $value = $value[$segment];
    }
    return $value;
}

function url(string $path = ''): string
{
    $base = rtrim((string) app_config('app.url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function auth_user(): ?array
{
    return Auth::user();
}

function can(string $permission): bool
{
    return Auth::can($permission);
}
