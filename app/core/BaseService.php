<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseService
{
    protected function respuestaCorrecta(array $datos = [], string $mensaje = ''): array
    {
        return [
            'correcto' => true,
            'mensaje' => $mensaje,
            'datos' => $datos,
        ];
    }

    protected function respuestaError(string $mensaje, array $errores = []): array
    {
        return [
            'correcto' => false,
            'mensaje' => $mensaje,
            'errores' => $errores,
        ];
    }
}
