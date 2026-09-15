<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">SOLICITUD</span>
        <h1 class="h3 mb-1">Aceptar solicitudes</h1>
        <p class="text-secondary mb-0">Solicitudes pendientes que requieren revisión y aceptación.</p>
    </div>
</div>

<div class="panel-card request-list-card">
    <div class="panel-card-header">
        <div>
            <span class="section-kicker">PENDIENTES</span>
            <h2 class="h5 mb-0">Solicitudes por aceptar</h2>
        </div>
        <i class="bi bi-check2-square panel-header-icon"></i>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
            <h3 class="h6">No hay solicitudes pendientes</h3>
            <p class="text-secondary mb-0">Cuando exista una solicitud pendiente aparecerá en este apartado.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive mt-3">
            <table class="table align-middle request-table mb-0">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Trabajador</th>
                        <th>Tipo</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><strong><?= e((string) $solicitud['FOLIO_SOLICITUD']) ?></strong></td>
                        <td><?= e($solicitud['NOMBRE_TRABAJADOR_COMPLETO'] ?: 'Trabajador #' . $solicitud['ID_TRABAJADOR']) ?></td>
                        <td><?= e($solicitud['TIPO_SOLICITUD_NOMBRE']) ?></td>
                        <td>Origen #<?= (int) $solicitud['ID_ORIGEN'] ?></td>
                        <td>Destino #<?= (int) $solicitud['ID_DESTINO'] ?></td>
                        <td><span class="request-status status-<?= e($solicitud['CLAVE_ESTATUS_SOLICITUD']) ?>"><?= e($solicitud['NOMBRE_ESTATUS_SOLICITUD']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
