<?php
$tarjetas = [
    ['clave' => 'pendiente', 'nombre' => 'Pendientes', 'icono' => 'bi-hourglass-split'],
    ['clave' => 'en_espera', 'nombre' => 'En espera', 'icono' => 'bi-clock-history'],
    ['clave' => 'programada', 'nombre' => 'Programadas', 'icono' => 'bi-calendar2-check-fill'],
    ['clave' => 'realizada', 'nombre' => 'Realizadas', 'icono' => 'bi-check-circle-fill'],
    ['clave' => 'cancelada', 'nombre' => 'Canceladas', 'icono' => 'bi-x-circle-fill'],
    ['clave' => 'vencida', 'nombre' => 'Vencidas', 'icono' => 'bi-calendar-x-fill'],
];
?>
<div class="dashboard-status-grid mb-4">
    <?php foreach ($tarjetas as $tarjeta): ?>
        <div class="status-card status-<?= e($tarjeta['clave']) ?>">
            <div class="status-card-icon"><i class="bi <?= e($tarjeta['icono']) ?>"></i></div>
            <div class="status-card-content">
                <div class="status-card-count"><?= (int) ($conteos[$tarjeta['clave']] ?? 0) ?></div>
                <div class="status-card-name"><?= e($tarjeta['nombre']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
