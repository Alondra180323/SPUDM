<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">ADMINISTRACIÓN GENERAL</span>
        <h1 class="h3 mb-1">Bienvenido, <?= e(auth_user()['nombre'] ?? 'Administrador') ?></h1>
        <p class="text-secondary mb-0">Vista global para supervisar solicitudes, programación, usuarios y operación.</p>
    </div>
    <span class="role-badge"><i class="bi bi-shield-fill-check"></i> Administrador</span>
</div>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Solicitudes del día', '0', 'bi-file-earmark-text-fill', 'Pendientes de flujo'],
        ['Unidades programadas', '0', 'bi-bus-front-fill', 'Asignaciones activas'],
        ['Usuarios activos', '0', 'bi-people-fill', 'Accesos habilitados'],
        ['Pendientes críticos', '0', 'bi-exclamation-triangle-fill', 'Requieren atención'],
    ];
    foreach ($cards as [$label, $value, $icon, $note]): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card h-100">
                <div class="metric-icon"><i class="bi <?= e($icon) ?>"></i></div>
                <div class="metric-value"><?= e($value) ?></div>
                <div class="metric-label"><?= e($label) ?></div>
                <div class="metric-note"><?= e($note) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require APP_PATH . '/views/dashboard/shared.php'; ?>
