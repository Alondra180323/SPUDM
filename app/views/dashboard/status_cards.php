<?php
$cards = [
    ['key' => 'pendiente', 'label' => 'Pendientes', 'icon' => 'bi-hourglass-split'],
    ['key' => 'en_espera', 'label' => 'En espera', 'icon' => 'bi-clock-history'],
    ['key' => 'programada', 'label' => 'Programadas', 'icon' => 'bi-calendar2-check-fill'],
    ['key' => 'realizada', 'label' => 'Realizadas', 'icon' => 'bi-check-circle-fill'],
    ['key' => 'cancelada', 'label' => 'Canceladas', 'icon' => 'bi-x-circle-fill'],
    ['key' => 'vencida', 'label' => 'Vencidas', 'icon' => 'bi-calendar-x-fill'],
];
?>
<div class="dashboard-status-grid mb-4">
    <?php foreach ($cards as $card): ?>
        <div class="status-card status-<?= e($card['key']) ?>">
            <div class="status-card-icon"><i class="bi <?= e($card['icon']) ?>"></i></div>
            <div class="status-card-content">
                <div class="status-card-count"><?= (int) ($counts[$card['key']] ?? 0) ?></div>
                <div class="status-card-name"><?= e($card['label']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
