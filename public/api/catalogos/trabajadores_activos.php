<?php

declare(strict_types=1);

use App\Core\ApiAutenticacion;
use App\Core\ManejadorApiBase;
use App\Services\CatalogoService;

require dirname(__DIR__) . '/inicializar.php';

ApiAutenticacion::requerirPermiso('catalogos.ver');

try {
    $trabajadores = (new CatalogoService())->trabajadoresActivos();

    $resultado = array_map(static function (array $trabajador): array {
        $nombreCompleto = trim(
            (string) ($trabajador['nombre'] ?? '') . ' ' .
            (string) ($trabajador['apellidos'] ?? '')
        );
        $numero = trim((string) ($trabajador['numero_trabajador'] ?? ''));

        return [
            'id' => (int) ($trabajador['id'] ?? 0),
            'texto' => trim(($numero !== '' ? $numero . ' · ' : '') . $nombreCompleto),
            'nombre' => $nombreCompleto,
            'numero_trabajador' => $numero,
        ];
    }, $trabajadores);

    ManejadorApiBase::responder([
        'correcto' => true,
        'trabajadores' => $resultado,
    ]);
} catch (Throwable $excepcion) {
    ManejadorApiBase::responder([
        'correcto' => false,
        'mensaje' => configuracion('app.debug')
            ? $excepcion->getMessage()
            : 'No fue posible consultar los trabajadores activos.',
    ], 500);
}
