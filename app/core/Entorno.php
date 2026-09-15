<?php

declare(strict_types=1);

namespace App\Core;

final class Entorno
{
    public static function cargar(string $archivo): void
    {
        if (!is_file($archivo)) {
            return;
        }

        foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) {
                continue;
            }

            [$clave, $valor] = array_map('trim', explode('=', $linea, 2));
            $valor = trim($valor, "\"'");

            if (getenv($clave) === false) {
                putenv("{$clave}={$valor}");
                $_ENV[$clave] = $valor;
                $_SERVER[$clave] = $valor;
            }
        }
    }
}
