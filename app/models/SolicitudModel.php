<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

final class SolicitudModel extends BaseModel
{
    private const CLAVES_ESTATUS = [
        'pendiente',
        'en_espera',
        'programada',
        'realizada',
        'cancelada',
        'vencida',
    ];

    public function conteosInicio(array $usuario): array
    {
        $conteos = array_fill_keys(self::CLAVES_ESTATUS, 0);

        try {
            $this->marcarVencidas();

            $condicion = '';
            $parametros = [];
            if (($usuario['rol_slug'] ?? '') === 'supervisor') {
                $condicion = ' WHERE ID_USUARIO_SOLICITANTE = :usuario_id ';
                $parametros['usuario_id'] = (int) ($usuario['id'] ?? 0);
            }

            $sql = "SELECT ID_ESTATUS_SOLICITUD, COUNT(ID_SOLICITUD) AS total
                    FROM tbl_solicitud_spudm
                    {$condicion}
                    GROUP BY ID_ESTATUS_SOLICITUD";

            $consulta = $this->conexion()->prepare($sql);
            $consulta->execute($parametros);

            $mapaEstatus = $this->mapaIdEstatusAClave();
            foreach ($consulta->fetchAll() as $row) {
                $idEstatus = (int) ($row['ID_ESTATUS_SOLICITUD'] ?? 0);
                $clave = $mapaEstatus[$idEstatus] ?? null;
                if ($clave !== null && array_key_exists($clave, $conteos)) {
                    $conteos[$clave] = (int) ($row['total'] ?? 0);
                }
            }
        } catch (PDOException) {
            // El dashboard permanece disponible aunque la tabla todavía no esté lista.
        }

        return $conteos;
    }

    public function registrar(array $datos): string
    {
        $conexion = $this->conexion();
        $conexion->beginTransaction();

        try {
            $folio = $this->generarFolio();
            $esAutoIncremental = $this->idEsAutoIncremental();

            $columnas = [
                'FOLIO_SOLICITUD',
                'FOLIO_VIAJE',
                'ID_TIPO_MOVIMIENTO',
                'ID_TIPO_PROGRAMA',
                'ID_CLIENTE',
                'ID_AREA',
                'ID_TRABAJADOR',
                'ID_ORIGEN',
                'ID_DESTINO',
                'ID_ESTATUS_SOLICITUD',
                'unidad_asignada',
                'ID_USUARIO_LABORALES',
                'ID_USUARIO_PROGRAMACION',
                'ID_USUARIO_SOLICITANTE',
                'FECHA_CREACION_CREACION',
                'HORA_CREACION_SOLICITUD',
            ];

            $marcadores = [
                ':folio_solicitud',
                'NULL',
                'NULL',
                ':tipo_programa',
                ':cliente',
                ':area',
                ':trabajador',
                ':origen',
                ':destino',
                ':estatus',
                'NULL',
                'NULL',
                'NULL',
                ':usuario_solicitante',
                ':fecha_creacion',
                ':hora_creacion',
            ];

            $parametros = [
                'folio_solicitud' => $folio,
                'tipo_programa' => $datos['tipo_programa_id'],
                'cliente' => $datos['cliente_id'],
                'area' => $datos['area_id'],
                'trabajador' => $datos['id_trabajador'],
                'origen' => $datos['id_origen'],
                'destino' => $datos['id_destino'],
                'estatus' => $datos['estatus_id'],
                'usuario_solicitante' => $datos['usuario_solicitante'],
                'fecha_creacion' => date('Y-m-d'),
                'hora_creacion' => date('H:i:s'),
            ];

            // Compatibilidad con la definición mostrada por el usuario, donde
            // ID_SOLICITUD todavía no aparece como AUTO_INCREMENT.
            if (!$esAutoIncremental) {
                array_unshift($columnas, 'ID_SOLICITUD');
                array_unshift($marcadores, ':id_solicitud');
                $parametros['id_solicitud'] = $this->siguienteIdSolicitud();
            }

            $sql = sprintf(
                'INSERT INTO tbl_solicitud_spudm (%s) VALUES (%s)',
                implode(', ', $columnas),
                implode(', ', $marcadores)
            );

            $consulta = $conexion->prepare($sql);
            $consulta->execute($parametros);

            $conexion->commit();
            return $folio;
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }
    }

    public function solicitudesRecientes(array $usuario, int $limite = 30): array
    {
        try {
            $this->marcarVencidas();

            $condicion = '';
            $parametros = [];
            if (($usuario['rol_slug'] ?? '') === 'supervisor') {
                $condicion = ' WHERE s.ID_USUARIO_SOLICITANTE = :usuario_id ';
                $parametros['usuario_id'] = (int) ($usuario['id'] ?? 0);
            }

            $limite = max(1, min(100, $limite));
            $sql = $this->consultaBaseListado() . " {$condicion}
                    ORDER BY s.ID_SOLICITUD DESC
                    LIMIT {$limite}";

            $consulta = $this->conexion()->prepare($sql);
            $consulta->execute($parametros);
            return $consulta->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function pendientesParaAceptar(int $limite = 100): array
    {
        try {
            $limite = max(1, min(200, $limite));
            $idPendiente = (int) configuracion('solicitudes.status.pendiente');

            $sql = $this->consultaBaseListado() . "
                    WHERE s.ID_ESTATUS_SOLICITUD = :pendiente
                    ORDER BY s.ID_SOLICITUD DESC
                    LIMIT {$limite}";

            $consulta = $this->conexion()->prepare($sql);
            $consulta->execute(['pendiente' => $idPendiente]);
            return $consulta->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    private function consultaBaseListado(): string
    {
        return "SELECT
                    s.ID_SOLICITUD,
                    s.FOLIO_SOLICITUD,
                    s.FOLIO_VIAJE,
                    s.ID_TIPO_MOVIMIENTO,
                    s.ID_TIPO_PROGRAMA,
                    s.ID_CLIENTE,
                    s.ID_AREA,
                    s.ID_TRABAJADOR,
                    s.ID_ORIGEN,
                    s.ID_DESTINO,
                    s.ID_ESTATUS_SOLICITUD,
                    s.unidad_asignada,
                    s.ID_USUARIO_LABORALES,
                    s.ID_USUARIO_PROGRAMACION,
                    s.ID_USUARIO_SOLICITANTE,
                    s.FECHA_ENTRADA,
                    s.HORA_ENTRADA,
                    s.FECHA_SALIDA,
                    s.HORA_SALIDA,
                    s.FECHA_CREACION_CREACION,
                    s.HORA_CREACION_SOLICITUD,
                    s.FECHA_VENCIMIENTO_SOLICITUD,
                    s.HORA_VENCIMIENTO_SOLICITUD,
                    t.NOMBRE_USUARIO AS NOMBRE_TRABAJADOR,
                    t.APELLIDOS_USUARIO AS APELLIDOS_TRABAJADOR,
                    t.NUMERO_TRABAJADOR_USUARIO AS NUMERO_TRABAJADOR
                FROM tbl_solicitud_spudm s
                LEFT JOIN tbl_usuarios_spudm t
                    ON t.ID_USUARIO_SPUDM = s.ID_TRABAJADOR";
    }

    public function marcarVencidas(): void
    {
        try {
            $idPendiente = (int) configuracion('solicitudes.status.pendiente');
            $idEspera = (int) configuracion('solicitudes.status.en_espera');
            $idVencida = (int) configuracion('solicitudes.status.vencida');

            $sql = "UPDATE tbl_solicitud_spudm
                    SET ID_ESTATUS_SOLICITUD = :vencida
                    WHERE ID_ESTATUS_SOLICITUD IN (:pendiente, :espera)
                      AND FECHA_VENCIMIENTO_SOLICITUD IS NOT NULL
                      AND HORA_VENCIMIENTO_SOLICITUD IS NOT NULL
                      AND TIMESTAMP(FECHA_VENCIMIENTO_SOLICITUD, HORA_VENCIMIENTO_SOLICITUD) < NOW()";

            $consulta = $this->conexion()->prepare($sql);
            $consulta->execute([
                'vencida' => $idVencida,
                'pendiente' => $idPendiente,
                'espera' => $idEspera,
            ]);
        } catch (PDOException) {
            // No interrumpe la navegación si todavía no hay fechas de vencimiento.
        }
    }

    private function mapaIdEstatusAClave(): array
    {
        return [
            (int) configuracion('solicitudes.status.pendiente') => 'pendiente',
            (int) configuracion('solicitudes.status.en_espera') => 'en_espera',
            (int) configuracion('solicitudes.status.programada') => 'programada',
            (int) configuracion('solicitudes.status.realizada') => 'realizada',
            (int) configuracion('solicitudes.status.cancelada') => 'cancelada',
            (int) configuracion('solicitudes.status.vencida') => 'vencida',
        ];
    }

    private function idEsAutoIncremental(): bool
    {
        $consulta = $this->conexion()->query("SHOW COLUMNS FROM tbl_solicitud_spudm LIKE 'ID_SOLICITUD'");
        $columna = $consulta->fetch();

        if (!$columna) {
            throw new RuntimeException('No existe la columna ID_SOLICITUD en tbl_solicitud_spudm.');
        }

        return str_contains(strtolower((string) ($columna['Extra'] ?? '')), 'auto_increment');
    }

    private function siguienteIdSolicitud(): int
    {
        $valor = $this->conexion()->query(
            'SELECT COALESCE(MAX(ID_SOLICITUD), 0) + 1 FROM tbl_solicitud_spudm'
        )->fetchColumn();

        return max(1, (int) $valor);
    }

    private function generarFolio(): string
    {
        return 'SOL-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
