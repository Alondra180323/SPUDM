<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Autenticacion;
use App\Core\BaseService;
use App\Models\UsuarioModel;

final class AutenticacionService extends BaseService
{
    public function __construct(private readonly UsuarioModel $usuarios = new UsuarioModel()) {}

    public function iniciarSesion(string $usuario, string $password): array
    {
        $registro = $this->usuarios->buscarPorUsuario($usuario);

        if (!$registro) {
            return ['correcto' => false, 'mensaje' => 'Usuario o contraseña incorrectos.'];
        }

        $passwordGuardado = (string) ($registro['password'] ?? '');
        if ($passwordGuardado === '') {
            return ['correcto' => false, 'mensaje' => 'El usuario no tiene una contraseña configurada.'];
        }

        $passwordCorrecto = false;
        $informacionHash = password_get_info($passwordGuardado);
        $esHash = !empty($informacionHash['algo']);

        if ($esHash) {
            $passwordCorrecto = password_verify($password, $passwordGuardado);

            if ($passwordCorrecto && password_needs_rehash($passwordGuardado, PASSWORD_DEFAULT)) {
                $this->usuarios->actualizarHashPassword(
                    (int) $registro['id'],
                    password_hash($password, PASSWORD_DEFAULT)
                );
            }
        } else {
            $passwordCorrecto = hash_equals($passwordGuardado, $password);

            if ($passwordCorrecto) {
                $this->usuarios->actualizarHashPassword(
                    (int) $registro['id'],
                    password_hash($password, PASSWORD_DEFAULT)
                );
            }
        }

        if (!$passwordCorrecto) {
            return ['correcto' => false, 'mensaje' => 'Usuario o contraseña incorrectos.'];
        }

        $rolesPermitidos = ['administrador', 'supervisor', 'laborales', 'programacion'];
        if (!in_array((string) $registro['rol_slug'], $rolesPermitidos, true)) {
            return ['correcto' => false, 'mensaje' => 'El usuario no tiene un rol válido para SPUDM.'];
        }

        Autenticacion::iniciarSesion($registro);

        return ['correcto' => true, 'mensaje' => 'Acceso correcto.'];
    }
}
