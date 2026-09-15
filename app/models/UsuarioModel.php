<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class UsuarioModel extends BaseModel
{
    public function buscarPorUsuario(string $usuario): ?array
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
                WHERE u.NUMERO_TRABAJADOR_USUARIO = :numero_trabajador
                   OR u.EMAIL_USUARIO = :correo
                   OR u.NOMBRE_USUARIO = :nombre
                LIMIT 1";

        $consulta = $this->conexion()->prepare($sql);
        $consulta->execute([
            'numero_trabajador' => $usuario,
            'correo' => $usuario,
            'nombre' => $usuario,
        ]);

        $registro = $consulta->fetch();
        if (!$registro) {
            return null;
        }

        $datosRol = $this->resolverRol((int) ($registro['ID_ROL_USUARIO'] ?? 0));
        $nombreCompleto = trim(
            (string) ($registro['NOMBRE_USUARIO'] ?? '') . ' ' .
            (string) ($registro['APELLIDOS_USUARIO'] ?? '')
        );

        return [
            'id' => (int) $registro['id'],
            'usuario' => (string) (
                $registro['NUMERO_TRABAJADOR_USUARIO']
                ?: $registro['EMAIL_USUARIO']
                ?: $registro['NOMBRE_USUARIO']
                ?: $usuario
            ),
            'nombre' => $nombreCompleto !== '' ? $nombreCompleto : $usuario,
            'password' => (string) ($registro['PASSWORD_USUARIO'] ?? ''),
            'rol_id' => isset($registro['ID_ROL_USUARIO']) ? (int) $registro['ID_ROL_USUARIO'] : 0,
            'rol' => $datosRol['nombre'],
            'rol_slug' => $datosRol['clave'],
            'estatus_id' => isset($registro['ID_ESTATUS_USUARIO']) ? (int) $registro['ID_ESTATUS_USUARIO'] : null,
            'cliente_id' => isset($registro['ID_CLIENTE_USUARIO']) ? (int) $registro['ID_CLIENTE_USUARIO'] : null,
            'area_id' => isset($registro['ID_AREA_USUARIO']) ? (int) $registro['ID_AREA_USUARIO'] : null,
            'email' => (string) ($registro['EMAIL_USUARIO'] ?? ''),
            'numero_trabajador' => (string) ($registro['NUMERO_TRABAJADOR_USUARIO'] ?? ''),
            'telefono' => (string) ($registro['NUMERO_TELEFONO_USUARIO'] ?? ''),
            'gerente_area' => (string) ($registro['GERENTE_AREA'] ?? ''),
            'foto' => (string) ($registro['RUTA_FOTO_USUARIO'] ?? ''),
        ];
    }

    public function actualizarHashPassword(int $idUsuario, string $hash): void
    {
        $sql = "UPDATE tbl_usuarios_spudm
                SET PASSWORD_USUARIO = :password,
                    FECHA_ACTUALIZACION_USUARIO = :fecha
                WHERE ID_USUARIO_SPUDM = :id_usuario
                LIMIT 1";

        $consulta = $this->conexion()->prepare($sql);
        $consulta->execute([
            'password' => $hash,
            'fecha' => date('Y-m-d H:i:s'),
            'id_usuario' => $idUsuario,
        ]);
    }

    private function resolverRol(int $idRol): array
    {
        $roles = [
            1 => ['nombre' => 'Administrador', 'clave' => 'administrador'],
            2 => ['nombre' => 'Supervisor', 'clave' => 'supervisor'],
            3 => ['nombre' => 'Laborales', 'clave' => 'laborales'],
            4 => ['nombre' => 'Programación', 'clave' => 'programacion'],
        ];

        return $roles[$idRol] ?? ['nombre' => 'Supervisor', 'clave' => 'supervisor'];
    }
}
