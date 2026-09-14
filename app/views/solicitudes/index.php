<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">SOLICITUD</span>
        <h1 class="h3 mb-1">Ver solicitudes</h1>
        <p class="text-secondary mb-0">Consulta las solicitudes registradas y su estado actual.</p>
    </div>
    <?php if (can('solicitudes.crear')): ?>
        <a href="<?= url('/solicitudes/nueva') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Registrar solicitud</a>
    <?php endif; ?>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= e($success) ?>
    </div>
<?php endif; ?>

<div class="panel-card request-list-card">
    <div class="panel-card-header">
        <div>
            <span class="section-kicker">REGISTROS</span>
            <h2 class="h5 mb-0">Solicitudes recientes</h2>
        </div>
        <i class="bi bi-file-earmark-text-fill panel-header-icon"></i>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
            <h3 class="h6">Todavía no hay solicitudes</h3>
            <p class="text-secondary mb-3">Registra la primera solicitud para comenzar a llevar el control.</p>
            <?php if (can('solicitudes.crear')): ?>
                <a href="<?= url('/solicitudes/nueva') ?>" class="btn btn-primary btn-sm">Registrar solicitud</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive mt-3">
            <table class="table align-middle request-table mb-0">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Trabajador</th>
                        <th>Tipo</th>
                        <th>Ruta</th>
                        <th>Estatus</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><strong><?= e((string) $solicitud['FOLIO_SOLICITUD']) ?></strong></td>
                        <td>
                            <div class="fw-semibold"><?= e($solicitud['NOMBRE_TRABAJADOR_COMPLETO'] ?: 'Trabajador #' . $solicitud['ID_TRABAJADOR']) ?></div>
                            <small class="text-secondary"><?= e($solicitud['NUMERO_TRABAJADOR'] ?: 'Sin número') ?></small>
                        </td>
                        <td><span class="request-type-badge"><?= e($solicitud['TIPO_SOLICITUD_NOMBRE']) ?></span></td>
                        <td class="route-cell">
                            <span><i class="bi bi-geo-alt-fill"></i>Origen #<?= (int) $solicitud['ID_ORIGEN'] ?></span>
                            <span><i class="bi bi-geo-fill"></i>Destino #<?= (int) $solicitud['ID_DESTINO'] ?></span>
                        </td>
                        <td><span class="request-status status-<?= e($solicitud['CLAVE_ESTATUS_SOLICITUD']) ?>"><?= e($solicitud['NOMBRE_ESTATUS_SOLICITUD']) ?></span></td>
                        <td><small class="text-secondary"><?= e(date('d/m/Y', strtotime((string) $solicitud['FECHA_CREACION_CREACION']))) ?> <?= e(substr((string) $solicitud['HORA_CREACION_SOLICITUD'], 0, 5)) ?></small></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
