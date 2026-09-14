<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SolicitudModel;
use App\Models\UserModel;
use Throwable;

final class SolicitudService
{
    public function __construct(
        private readonly SolicitudModel $solicitudes = new SolicitudModel(),
        private readonly UserModel $users = new UserModel()
    ) {}

    public function dashboardCounts(array $user): array
    {
        return $this->solicitudes->dashboardCounts($user);
    }

    public function recent(array $user, int $limit = 30): array
    {
        return array_map(fn(array $row): array => $this->decorate($row), $this->solicitudes->recent($user, $limit));
    }

    public function pendingForAcceptance(int $limit = 100): array
    {
        return array_map(fn(array $row): array => $this->decorate($row), $this->solicitudes->pendingForAcceptance($limit));
    }

    public function activeWorkers(): array
    {
        try {
            return $this->users->activeWorkers();
        } catch (Throwable) {
            return [];
        }
    }

    public function create(array $input, array $user): array
    {
        $tipo = strtoupper(trim((string) ($input['tipo_solicitud'] ?? '')));
        $workerId = (int) ($input['id_trabajador'] ?? 0);
        $originId = (int) ($input['id_origen'] ?? 0);
        $destinationId = (int) ($input['id_destino'] ?? 0);

        $programTypeIds = [
            'UNICO' => (int) app_config('solicitudes.tipo_programa.unico'),
            'FIJO' => (int) app_config('solicitudes.tipo_programa.fijo'),
        ];

        $data = [
            'tipo_solicitud' => $tipo,
            'tipo_programa_id' => $programTypeIds[$tipo] ?? 0,
            'id_trabajador' => $workerId,
            'id_origen' => $originId,
            'id_destino' => $destinationId,
            'estatus_id' => (int) app_config('solicitudes.status.pendiente'),
            'usuario_solicitante' => (int) ($user['id'] ?? 0),
            'cliente_id' => isset($user['cliente_id']) && $user['cliente_id'] !== null ? (int) $user['cliente_id'] : null,
            'area_id' => isset($user['area_id']) && $user['area_id'] !== null ? (int) $user['area_id'] : null,
        ];

        $errors = [];
        if (!isset($programTypeIds[$tipo])) {
            $errors['tipo_solicitud'] = 'Selecciona si la solicitud es Único o Fijo.';
        }
        if ($workerId <= 0) {
            $errors['id_trabajador'] = 'Selecciona un trabajador activo.';
        }
        if ($originId <= 0) {
            $errors['id_origen'] = 'Ingresa un ID de origen válido.';
        }
        if ($destinationId <= 0) {
            $errors['id_destino'] = 'Ingresa un ID de destino válido.';
        }
        if ($originId > 0 && $originId === $destinationId) {
            $errors['id_destino'] = 'El origen y el destino deben ser diferentes.';
        }
        if ($data['usuario_solicitante'] <= 0) {
            $errors['general'] = 'No fue posible identificar al usuario que registra la solicitud.';
        }

        if ($workerId > 0) {
            try {
                $worker = $this->users->findActiveWorkerById($workerId);
            } catch (Throwable) {
                $worker = null;
            }
            if (!$worker) {
                $errors['id_trabajador'] = 'El trabajador seleccionado no está activo.';
            }
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors, 'old' => $data];
        }

        try {
            $folio = $this->solicitudes->create($data);
            return ['ok' => true, 'folio' => $folio];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'errors' => [
                    'general' => app_config('app.debug')
                        ? 'No fue posible registrar la solicitud: ' . $e->getMessage()
                        : 'No fue posible registrar la solicitud. Revisa la configuración de IDs y la estructura de tbl_solicitud_spudm.',
                ],
                'old' => $data,
            ];
        }
    }

    private function decorate(array $row): array
    {
        $statusId = (int) ($row['ID_ESTATUS_SOLICITUD'] ?? 0);
        $status = $this->statusInfo($statusId);
        $programId = (int) ($row['ID_TIPO_PROGRAMA'] ?? 0);

        $row['CLAVE_ESTATUS_SOLICITUD'] = $status['key'];
        $row['NOMBRE_ESTATUS_SOLICITUD'] = $status['label'];
        $row['TIPO_SOLICITUD_NOMBRE'] = $this->programTypeLabel($programId);
        $row['NOMBRE_TRABAJADOR_COMPLETO'] = trim(
            (string) ($row['NOMBRE_TRABAJADOR'] ?? '') . ' ' .
            (string) ($row['APELLIDOS_TRABAJADOR'] ?? '')
        );

        return $row;
    }

    private function statusInfo(int $id): array
    {
        $map = [
            (int) app_config('solicitudes.status.pendiente') => ['key' => 'pendiente', 'label' => 'Pendiente'],
            (int) app_config('solicitudes.status.en_espera') => ['key' => 'en_espera', 'label' => 'En espera'],
            (int) app_config('solicitudes.status.programada') => ['key' => 'programada', 'label' => 'Programada'],
            (int) app_config('solicitudes.status.realizada') => ['key' => 'realizada', 'label' => 'Realizada'],
            (int) app_config('solicitudes.status.cancelada') => ['key' => 'cancelada', 'label' => 'Cancelada'],
            (int) app_config('solicitudes.status.vencida') => ['key' => 'vencida', 'label' => 'Vencida'],
        ];

        return $map[$id] ?? ['key' => 'desconocido', 'label' => 'Estatus #' . $id];
    }

    private function programTypeLabel(int $id): string
    {
        if ($id === (int) app_config('solicitudes.tipo_programa.unico')) {
            return 'Único';
        }
        if ($id === (int) app_config('solicitudes.tipo_programa.fijo')) {
            return 'Fijo';
        }
        return $id > 0 ? 'Tipo #' . $id : 'Sin tipo';
    }
}
