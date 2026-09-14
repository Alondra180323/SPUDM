<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

final class SolicitudModel
{
    private const STATUS_KEYS = [
        'pendiente',
        'en_espera',
        'programada',
        'realizada',
        'cancelada',
        'vencida',
    ];

    public function dashboardCounts(array $user): array
    {
        $counts = array_fill_keys(self::STATUS_KEYS, 0);

        try {
            $this->markExpired();

            $where = '';
            $params = [];
            if (($user['rol_slug'] ?? '') === 'supervisor') {
                $where = ' WHERE ID_USUARIO_SOLICITANTE = :usuario_id ';
                $params['usuario_id'] = (int) ($user['id'] ?? 0);
            }

            $sql = "SELECT ID_ESTATUS_SOLICITUD, COUNT(ID_SOLICITUD) AS total
                    FROM tbl_solicitud_spudm
                    {$where}
                    GROUP BY ID_ESTATUS_SOLICITUD";

            $stmt = Database::connection()->prepare($sql);
            $stmt->execute($params);

            $statusMap = $this->statusIdToKeyMap();
            foreach ($stmt->fetchAll() as $row) {
                $statusId = (int) ($row['ID_ESTATUS_SOLICITUD'] ?? 0);
                $key = $statusMap[$statusId] ?? null;
                if ($key !== null && array_key_exists($key, $counts)) {
                    $counts[$key] = (int) ($row['total'] ?? 0);
                }
            }
        } catch (PDOException) {
            // El dashboard permanece disponible aunque la tabla todavía no esté lista.
        }

        return $counts;
    }

    public function create(array $data): string
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $folio = $this->generateFolio();
            $hasAutoIncrement = $this->idIsAutoIncrement();

            $columns = [
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

            $placeholders = [
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

            $params = [
                'folio_solicitud' => $folio,
                'tipo_programa' => $data['tipo_programa_id'],
                'cliente' => $data['cliente_id'],
                'area' => $data['area_id'],
                'trabajador' => $data['id_trabajador'],
                'origen' => $data['id_origen'],
                'destino' => $data['id_destino'],
                'estatus' => $data['estatus_id'],
                'usuario_solicitante' => $data['usuario_solicitante'],
                'fecha_creacion' => date('Y-m-d'),
                'hora_creacion' => date('H:i:s'),
            ];

            // Compatibilidad con la definición mostrada por el usuario, donde
            // ID_SOLICITUD todavía no aparece como AUTO_INCREMENT.
            if (!$hasAutoIncrement) {
                array_unshift($columns, 'ID_SOLICITUD');
                array_unshift($placeholders, ':id_solicitud');
                $params['id_solicitud'] = $this->nextSolicitudId();
            }

            $sql = sprintf(
                'INSERT INTO tbl_solicitud_spudm (%s) VALUES (%s)',
                implode(', ', $columns),
                implode(', ', $placeholders)
            );

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $pdo->commit();
            return $folio;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function recent(array $user, int $limit = 30): array
    {
        try {
            $this->markExpired();

            $where = '';
            $params = [];
            if (($user['rol_slug'] ?? '') === 'supervisor') {
                $where = ' WHERE s.ID_USUARIO_SOLICITANTE = :usuario_id ';
                $params['usuario_id'] = (int) ($user['id'] ?? 0);
            }

            $limit = max(1, min(100, $limit));
            $sql = $this->baseListQuery() . " {$where}
                    ORDER BY s.ID_SOLICITUD DESC
                    LIMIT {$limit}";

            $stmt = Database::connection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function pendingForAcceptance(int $limit = 100): array
    {
        try {
            $limit = max(1, min(200, $limit));
            $pendingId = (int) app_config('solicitudes.status.pendiente');

            $sql = $this->baseListQuery() . "
                    WHERE s.ID_ESTATUS_SOLICITUD = :pendiente
                    ORDER BY s.ID_SOLICITUD DESC
                    LIMIT {$limit}";

            $stmt = Database::connection()->prepare($sql);
            $stmt->execute(['pendiente' => $pendingId]);
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    private function baseListQuery(): string
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

    public function markExpired(): void
    {
        try {
            $pendingId = (int) app_config('solicitudes.status.pendiente');
            $waitingId = (int) app_config('solicitudes.status.en_espera');
            $expiredId = (int) app_config('solicitudes.status.vencida');

            $sql = "UPDATE tbl_solicitud_spudm
                    SET ID_ESTATUS_SOLICITUD = :vencida
                    WHERE ID_ESTATUS_SOLICITUD IN (:pendiente, :espera)
                      AND FECHA_VENCIMIENTO_SOLICITUD IS NOT NULL
                      AND HORA_VENCIMIENTO_SOLICITUD IS NOT NULL
                      AND TIMESTAMP(FECHA_VENCIMIENTO_SOLICITUD, HORA_VENCIMIENTO_SOLICITUD) < NOW()";

            $stmt = Database::connection()->prepare($sql);
            $stmt->execute([
                'vencida' => $expiredId,
                'pendiente' => $pendingId,
                'espera' => $waitingId,
            ]);
        } catch (PDOException) {
            // No interrumpe la navegación si todavía no hay fechas de vencimiento.
        }
    }

    private function statusIdToKeyMap(): array
    {
        return [
            (int) app_config('solicitudes.status.pendiente') => 'pendiente',
            (int) app_config('solicitudes.status.en_espera') => 'en_espera',
            (int) app_config('solicitudes.status.programada') => 'programada',
            (int) app_config('solicitudes.status.realizada') => 'realizada',
            (int) app_config('solicitudes.status.cancelada') => 'cancelada',
            (int) app_config('solicitudes.status.vencida') => 'vencida',
        ];
    }

    private function idIsAutoIncrement(): bool
    {
        $stmt = Database::connection()->query("SHOW COLUMNS FROM tbl_solicitud_spudm LIKE 'ID_SOLICITUD'");
        $column = $stmt->fetch();

        if (!$column) {
            throw new RuntimeException('No existe la columna ID_SOLICITUD en tbl_solicitud_spudm.');
        }

        return str_contains(strtolower((string) ($column['Extra'] ?? '')), 'auto_increment');
    }

    private function nextSolicitudId(): int
    {
        $value = Database::connection()->query(
            'SELECT COALESCE(MAX(ID_SOLICITUD), 0) + 1 FROM tbl_solicitud_spudm'
        )->fetchColumn();

        return max(1, (int) $value);
    }

    private function generateFolio(): string
    {
        return 'SOL-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
