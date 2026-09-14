<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Models\UserModel;

final class AuthService
{
    public function __construct(private readonly UserModel $users = new UserModel()) {}

    public function attempt(string $username, string $password): array
    {
        $user = $this->users->findByUsername($username);

        if (!$user) {
            return ['ok' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }

        $storedPassword = (string) ($user['password'] ?? '');
        if ($storedPassword === '') {
            return ['ok' => false, 'message' => 'El usuario no tiene una contraseña configurada.'];
        }

        $passwordOk = false;
        $passwordInfo = password_get_info($storedPassword);
        $isModernHash = !empty($passwordInfo['algo']);

        if ($isModernHash) {
            $passwordOk = password_verify($password, $storedPassword);

            if ($passwordOk && password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
                $this->users->updatePasswordHash(
                    (int) $user['id'],
                    password_hash($password, PASSWORD_DEFAULT)
                );
            }
        } else {
            // Compatibilidad temporal si el usuario fue registrado manualmente
            // con la contraseña sin hash. En el primer acceso se migra a hash.
            $passwordOk = hash_equals($storedPassword, $password);

            if ($passwordOk) {
                $this->users->updatePasswordHash(
                    (int) $user['id'],
                    password_hash($password, PASSWORD_DEFAULT)
                );
            }
        }

        if (!$passwordOk) {
            return ['ok' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }

        $allowedRoles = ['administrador', 'supervisor', 'laborales', 'programacion'];
        if (!in_array((string) $user['rol_slug'], $allowedRoles, true)) {
            return ['ok' => false, 'message' => 'El usuario no tiene un rol válido para SPUDM.'];
        }

        Auth::login($user);

        return ['ok' => true, 'message' => 'Acceso correcto.'];
    }
}
