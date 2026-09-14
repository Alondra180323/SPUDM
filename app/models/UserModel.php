<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class UserModel
{
    /**
     * Busca al usuario directamente en tbl_usuarios_spudm.
     * El campo "Usuario" del login puede ser:
     * - NUMERO_TRABAJADOR_USUARIO
     * - EMAIL_USUARIO
     * - NOMBRE_USUARIO
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT
                    u.ID_USUARIO_SPUDM AS id,
                    u.NOMBRE_USUARIO,
                    u.APELLIDOS_USUARIO,
                    u.NUMERO_TELEFONO_USUARIO,
                    u.NUMERO_TRABAJADOR_USUARIO,
                    u.ID_CLIENTE_USUARIO,
                    u.ID_AREA_USUARIO,
                    u.ID_ESTATUS_USUARIO,
                    u.ID_ROL_USUARIO,
                    u.EMAIL_USUARIO,
                    u.PASSWORD_USUARIO,
                    u.GERENTE_AREA,
                    u.RUTA_FOTO_USUARIO
                FROM tbl_usuarios_spudm u
                WHERE
                    u.NUMERO_TRABAJADOR_USUARIO = :login_trabajador
                    OR u.EMAIL_USUARIO = :login_email
                    OR u.NOMBRE_USUARIO = :login_nombre
                LIMIT 1";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'login_trabajador' => $username,
            'login_email' => $username,
            'login_nombre' => $username,
        ]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $roleData = $this->resolveRole(isset($row['ID_ROL_USUARIO']) ? (int) $row['ID_ROL_USUARIO'] : 0);
        $fullName = trim(
            (string) ($row['NOMBRE_USUARIO'] ?? '') . ' ' .
            (string) ($row['APELLIDOS_USUARIO'] ?? '')
        );

        return [
            'id' => (int) $row['id'],
            'usuario' => (string) (
                $row['NUMERO_TRABAJADOR_USUARIO']
                ?: $row['EMAIL_USUARIO']
                ?: $row['NOMBRE_USUARIO']
                ?: $username
            ),
            'nombre' => $fullName !== '' ? $fullName : $username,
            'password' => (string) ($row['PASSWORD_USUARIO'] ?? ''),
            'rol_id' => isset($row['ID_ROL_USUARIO']) ? (int) $row['ID_ROL_USUARIO'] : 0,
            'rol' => $roleData['name'],
            'rol_slug' => $roleData['slug'],
            'estatus_id' => isset($row['ID_ESTATUS_USUARIO']) ? (int) $row['ID_ESTATUS_USUARIO'] : null,
            'cliente_id' => isset($row['ID_CLIENTE_USUARIO']) ? (int) $row['ID_CLIENTE_USUARIO'] : null,
            'area_id' => isset($row['ID_AREA_USUARIO']) ? (int) $row['ID_AREA_USUARIO'] : null,
            'email' => (string) ($row['EMAIL_USUARIO'] ?? ''),
            'numero_trabajador' => (string) ($row['NUMERO_TRABAJADOR_USUARIO'] ?? ''),
            'telefono' => (string) ($row['NUMERO_TELEFONO_USUARIO'] ?? ''),
            'gerente_area' => (string) ($row['GERENTE_AREA'] ?? ''),
            'foto' => (string) ($row['RUTA_FOTO_USUARIO'] ?? ''),
        ];
    }

    /**
     * Si el usuario fue capturado manualmente con contraseña en texto plano,
     * al iniciar sesión correctamente se actualiza automáticamente a password_hash().
     */
    public function updatePasswordHash(int $userId, string $hash): void
    {
        $sql = "UPDATE tbl_usuarios_spudm
                SET PASSWORD_USUARIO = :password,
                    FECHA_ACTUALIZACION_USUARIO = :fecha
                WHERE ID_USUARIO_SPUDM = :id
                LIMIT 1";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'password' => $hash,
            'fecha' => date('Y-m-d H:i:s'),
            'id' => $userId,
        ]);
    }

    /**
     * Asignación inicial mientras se confirma la tabla real de roles.
     * Si llega un ID distinto, se usa Supervisor como perfil temporal restringido.
     */
    private function resolveRole(int $roleId): array
    {
        $roles = [
            1 => ['name' => 'Administrador', 'slug' => 'administrador'],
            2 => ['name' => 'Supervisor', 'slug' => 'supervisor'],
            3 => ['name' => 'Laborales', 'slug' => 'laborales'],
            4 => ['name' => 'Programación', 'slug' => 'programacion'],
        ];

        return $roles[$roleId] ?? ['name' => 'Supervisor', 'slug' => 'supervisor'];
    }
}
