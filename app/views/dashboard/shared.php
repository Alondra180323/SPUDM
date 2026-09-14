<div class="row g-4">
    <div class="col-xl-8">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <div>
                    <span class="section-kicker">FLUJO OPERATIVO</span>
                    <h2 class="h5 mb-0">Proceso de una solicitud</h2>
                </div>
                <i class="bi bi-diagram-3-fill panel-header-icon"></i>
            </div>
            <div class="process-flow">
                <div class="process-step"><span>1</span><div><strong>Solicitud</strong><small>Se registra el traslado</small></div></div>
                <i class="bi bi-chevron-right"></i>
                <div class="process-step"><span>2</span><div><strong>En espera</strong><small>Se revisa la petición</small></div></div>
                <i class="bi bi-chevron-right"></i>
                <div class="process-step"><span>3</span><div><strong>Programación</strong><small>Se asigna unidad</small></div></div>
                <i class="bi bi-chevron-right"></i>
                <div class="process-step"><span>4</span><div><strong>Realización</strong><small>Servicio completado</small></div></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <div>
                    <span class="section-kicker">ACCESO RÁPIDO</span>
                    <h2 class="h5 mb-0">Solicitudes</h2>
                </div>
            </div>
            <div class="quick-actions">
                <?php if (can('solicitudes.crear')): ?>
                    <a class="quick-action" href="<?= url('/solicitudes/nueva') ?>"><i class="bi bi-file-earmark-plus-fill"></i><span>Registrar solicitud</span></a>
                <?php endif; ?>
                <?php if (can('solicitudes.ver')): ?>
                    <a class="quick-action" href="<?= url('/solicitudes') ?>"><i class="bi bi-list-ul"></i><span>Ver solicitudes</span></a>
                <?php endif; ?>
                <?php if (can('solicitudes.validar')): ?>
                    <a class="quick-action" href="<?= url('/solicitudes/aceptar') ?>"><i class="bi bi-check2-square"></i><span>Aceptar solicitudes</span></a>
                <?php endif; ?>
                <button class="quick-action" disabled><i class="bi bi-calendar2-week-fill"></i><span>Programación</span></button>
            </div>
        </div>
    </div>
</div>
