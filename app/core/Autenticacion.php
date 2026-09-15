<?php

declare(strict_types=1);

namespace App\Core;

final class Autenticacion
{
    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['auth_user']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['auth_user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['auth_user']['id']) ? (int) $_SESSION['auth_user']['id'] : null;
    }

    public static function rol(): ?string
    {
        return $_SESSION['auth_user']['rol_slug'] ?? null;
    }

    public static function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true);

        $_SESSION['auth_user'] = [
            'id' => (int) $usuario['id'],
            'usuario' => (string) $usuario['usuario'],
            'nombre' => (string) ($usuario['nombre'] ?? $usuario['usuario']),
            'rol_id' => isset($usuario['rol_id']) ? (int) $usuario['rol_id'] : null,
            'rol' => (string) ($usuario['rol'] ?? ''),
            'rol_slug' => (string) $usuario['rol_slug'],
            'estatus_id' => $usuario['estatus_id'] ?? null,
            'cliente_id' => $usuario['cliente_id'] ?? null,
            'area_id' => $usuario['area_id'] ?? null,
            'email' => (string) ($usuario['email'] ?? ''),
            'numero_trabajador' => (string) ($usuario['numero_trabajador'] ?? ''),
            'foto' => (string) ($usuario['foto'] ?? ''),
        ];
    }

    public static function cerrarSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
    }

    public static function puede(string $permiso): bool
    {
        $rol = self::rol();
        if (!$rol) {
            return false;
        }

        $permisos = require APP_PATH . '/config/permisos.php';
        $otorgados = $permisos[$rol] ?? [];

        return in_array('*', $otorgados, true) || in_array($permiso, $otorgados, true);
    }
}
