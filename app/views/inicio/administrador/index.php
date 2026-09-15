<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">ADMINISTRACIÓN GENERAL</span>
        <h1 class="h3 mb-1">Bienvenido, <?= e(usuario_autenticado()['nombre'] ?? 'Administrador') ?></h1>
        <p class="text-secondary mb-0">Vista global para supervisar solicitudes y la operación del sistema.</p>
    </div>
    <span class="role-badge"><i class="bi bi-shield-fill-check"></i> Administrador</span>
</div>

<?php require APP_PATH . '/views/inicio/tarjetas_estatus.php'; ?>
<?php require APP_PATH . '/views/inicio/compartido.php'; ?>
