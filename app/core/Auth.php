<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['auth_user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['auth_user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['auth_user']['id']) ? (int) $_SESSION['auth_user']['id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['auth_user']['rol_slug'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['auth_user'] = [
            'id' => (int) $user['id'],
            'usuario' => (string) $user['usuario'],
            'nombre' => (string) ($user['nombre'] ?? $user['usuario']),
            'rol_id' => isset($user['rol_id']) ? (int) $user['rol_id'] : null,
            'rol' => (string) ($user['rol'] ?? ''),
            'rol_slug' => (string) $user['rol_slug'],
            'estatus_id' => $user['estatus_id'] ?? null,
            'cliente_id' => $user['cliente_id'] ?? null,
            'area_id' => $user['area_id'] ?? null,
            'email' => (string) ($user['email'] ?? ''),
            'numero_trabajador' => (string) ($user['numero_trabajador'] ?? ''),
            'foto' => (string) ($user['foto'] ?? ''),
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function can(string $permission): bool
    {
        $role = self::role();
        if (!$role) {
            return false;
        }

        $permissions = require APP_PATH . '/config/permissions.php';
        $granted = $permissions[$role] ?? [];

        return in_array('*', $granted, true) || in_array($permission, $granted, true);
    }
}
