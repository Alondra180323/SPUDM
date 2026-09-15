<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\BaseService;
use App\Models\SolicitudModel;
use Throwable;

final class SolicitudService extends BaseService
{
    public function __construct(
        private readonly SolicitudModel $solicitudes = new SolicitudModel(),
        private readonly CatalogoService $catalogos = new CatalogoService()
    ) {}

    public function conteosInicio(array $usuario): array
    {
        return $this->solicitudes->conteosInicio($usuario);
    }

    public function solicitudesRecientes(array $usuario, int $limite = 30): array
    {
        return array_map(
            fn(array $registro): array => $this->decorarSolicitud($registro),
            $this->solicitudes->solicitudesRecientes($usuario, $limite)
        );
    }

    public function pendientesParaAceptar(int $limite = 100): array
    {
        return array_map(
            fn(array $registro): array => $this->decorarSolicitud($registro),
            $this->solicitudes->pendientesParaAceptar($limite)
        );
    }

    public function trabajadoresActivos(): array
    {
        return $this->catalogos->trabajadoresActivos();
    }

    public function registrarSolicitud(array $entrada, array $usuario): array
    {
        $tipo = strtoupper(trim((string) ($entrada['tipo_solicitud'] ?? '')));
        $idTrabajador = (int) ($entrada['id_trabajador'] ?? 0);
        $idOrigen = (int) ($entrada['id_origen'] ?? 0);
        $idDestino = (int) ($entrada['id_destino'] ?? 0);

        $idsTipoPrograma = [
            'UNICO' => (int) configuracion('solicitudes.tipo_programa.unico'),
            'FIJO' => (int) configuracion('solicitudes.tipo_programa.fijo'),
        ];

        $datos = [
            'tipo_solicitud' => $tipo,
            'tipo_programa_id' => $idsTipoPrograma[$tipo] ?? 0,
            'id_trabajador' => $idTrabajador,
            'id_origen' => $idOrigen,
            'id_destino' => $idDestino,
            'estatus_id' => (int) configuracion('solicitudes.status.pendiente'),
            'usuario_solicitante' => (int) ($usuario['id'] ?? 0),
            'cliente_id' => isset($usuario['cliente_id']) && $usuario['cliente_id'] !== null ? (int) $usuario['cliente_id'] : null,
            'area_id' => isset($usuario['area_id']) && $usuario['area_id'] !== null ? (int) $usuario['area_id'] : null,
        ];

        $errores = [];

        if (!isset($idsTipoPrograma[$tipo])) {
            $errores['tipo_solicitud'] = 'Selecciona si la solicitud es Único o Fijo.';
        }

        if ($idTrabajador <= 0) {
            $errores['id_trabajador'] = 'Selecciona un trabajador activo.';
        }

        if ($idOrigen <= 0) {
            $errores['id_origen'] = 'Ingresa un ID de origen válido.';
        }

        if ($idDestino <= 0) {
            $errores['id_destino'] = 'Ingresa un ID de destino válido.';
        }

        if ($idOrigen > 0 && $idOrigen === $idDestino) {
            $errores['id_destino'] = 'El origen y el destino deben ser diferentes.';
        }

        if ($datos['usuario_solicitante'] <= 0) {
            $errores['general'] = 'No fue posible identificar al usuario que registra la solicitud.';
        }

        if ($idTrabajador > 0) {
            try {
                $trabajador = $this->catalogos->buscarTrabajadorActivoPorId($idTrabajador);
            } catch (Throwable $excepcion) {
                $trabajador = null;
                if (configuracion('app.debug')) {
                    $errores['general'] = 'Error al consultar trabajadores activos: ' . $excepcion->getMessage();
                }
            }

            if (!$trabajador && !isset($errores['general'])) {
                $errores['id_trabajador'] = 'El trabajador seleccionado no está activo.';
            }
        }

        if ($errores) {
            return ['correcto' => false, 'errores' => $errores, 'anterior' => $datos];
        }

        try {
            $folio = $this->solicitudes->registrar($datos);
            return ['correcto' => true, 'folio' => $folio];
        } catch (Throwable $excepcion) {
            return [
                'correcto' => false,
                'errores' => [
                    'general' => configuracion('app.debug')
                        ? 'No fue posible registrar la solicitud: ' . $excepcion->getMessage()
                        : 'No fue posible registrar la solicitud.',
                ],
                'anterior' => $datos,
            ];
        }
    }

    private function decorarSolicitud(array $registro): array
    {
        $idEstatus = (int) ($registro['ID_ESTATUS_SOLICITUD'] ?? 0);
        $estatus = $this->informacionEstatus($idEstatus);
        $idTipoPrograma = (int) ($registro['ID_TIPO_PROGRAMA'] ?? 0);

        $registro['CLAVE_ESTATUS_SOLICITUD'] = $estatus['clave'];
        $registro['NOMBRE_ESTATUS_SOLICITUD'] = $estatus['nombre'];
        $registro['TIPO_SOLICITUD_NOMBRE'] = $this->nombreTipoPrograma($idTipoPrograma);
        $registro['NOMBRE_TRABAJADOR_COMPLETO'] = trim(
            (string) ($registro['NOMBRE_TRABAJADOR'] ?? '') . ' ' .
            (string) ($registro['APELLIDOS_TRABAJADOR'] ?? '')
        );

        return $registro;
    }

    private function informacionEstatus(int $idEstatus): array
    {
        $mapa = [
            (int) configuracion('solicitudes.status.pendiente') => ['clave' => 'pendiente', 'nombre' => 'Pendiente'],
            (int) configuracion('solicitudes.status.en_espera') => ['clave' => 'en_espera', 'nombre' => 'En espera'],
            (int) configuracion('solicitudes.status.programada') => ['clave' => 'programada', 'nombre' => 'Programada'],
            (int) configuracion('solicitudes.status.realizada') => ['clave' => 'realizada', 'nombre' => 'Realizada'],
            (int) configuracion('solicitudes.status.cancelada') => ['clave' => 'cancelada', 'nombre' => 'Cancelada'],
            (int) configuracion('solicitudes.status.vencida') => ['clave' => 'vencida', 'nombre' => 'Vencida'],
        ];

        return $mapa[$idEstatus] ?? ['clave' => 'desconocido', 'nombre' => 'Estatus #' . $idEstatus];
    }

    private function nombreTipoPrograma(int $idTipoPrograma): string
    {
        if ($idTipoPrograma === (int) configuracion('solicitudes.tipo_programa.unico')) {
            return 'Único';
        }

        if ($idTipoPrograma === (int) configuracion('solicitudes.tipo_programa.fijo')) {
            return 'Fijo';
        }

        return $idTipoPrograma > 0 ? 'Tipo #' . $idTipoPrograma : 'Sin tipo';
    }
}
