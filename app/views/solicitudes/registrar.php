<?php
$errorCampo = static fn(string $campo): string => isset($errores[$campo]) ? ' is-invalid' : '';
?>
<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">SOLICITUD</span>
        <h1 class="h3 mb-1">Registro de Solicitud</h1>
        <p class="text-secondary mb-0">Captura los datos básicos para registrar una nueva solicitud de traslado.</p>
    </div>
    <a href="<?= url('/solicitudes') ?>" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Ver solicitudes</a>
</div>

<?php if (!empty($errores['general'])): ?>
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= escapar($errores['general']) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= url('/solicitudes') ?>" class="request-form" novalidate>
    <?= campo_csrf() ?>

    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon"><i class="bi bi-file-earmark-plus-fill"></i></div>
            <div>
                <span class="section-kicker">REGISTRO</span>
                <h2 class="h5 mb-0">Datos de la solicitud</h2>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-lg-4">
                <label for="tipo_solicitud" class="form-label fw-semibold">Tipo de programa <span class="text-danger">*</span></label>
                <select class="form-select<?= $errorCampo('tipo_solicitud') ?>" id="tipo_solicitud" name="tipo_solicitud" required>
                    <option value="">Selecciona...</option>
                    <option value="UNICO" <?= (($anterior['tipo_solicitud'] ?? '') === 'UNICO') ? 'selected' : '' ?>>Único</option>
                    <option value="FIJO" <?= (($anterior['tipo_solicitud'] ?? '') === 'FIJO') ? 'selected' : '' ?>>Fijo</option>
                </select>
                <?php if (isset($errores['tipo_solicitud'])): ?><div class="invalid-feedback"><?= escapar($errores['tipo_solicitud']) ?></div><?php endif; ?>
            </div>

            <div class="col-12 col-lg-8">
                <label for="id_trabajador" class="form-label fw-semibold">Trabajador activo <span class="text-danger">*</span></label>
                <select
                    class="form-select select2-trabajador<?= $errorCampo('id_trabajador') ?>"
                    id="id_trabajador"
                    name="id_trabajador"
                    data-placeholder="Buscar trabajador..."
                    data-cargar-trabajadores="1"
                    required
                >
                    <option value=""></option>
                    <?php foreach ($trabajadores as $trabajador): ?>
                        <?php
                        $idTrabajador = (int) ($trabajador['id'] ?? 0);
                        $nombreCompleto = trim((string) ($trabajador['nombre'] ?? '') . ' ' . (string) ($trabajador['apellidos'] ?? ''));
                        $numeroTrabajador = trim((string) ($trabajador['numero_trabajador'] ?? ''));
                        $textoTrabajador = ($numeroTrabajador !== '' ? $numeroTrabajador . ' · ' : '') . ($nombreCompleto !== '' ? $nombreCompleto : 'Trabajador');
                        ?>
                        <option value="<?= $idTrabajador ?>" <?= ((int) ($anterior['id_trabajador'] ?? 0) === $idTrabajador) ? 'selected' : '' ?>><?= escapar($textoTrabajador) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['id_trabajador'])): ?><div class="invalid-feedback d-block"><?= escapar($errores['id_trabajador']) ?></div><?php endif; ?>
                <?php if (empty($trabajadores)): ?>
                    <div class="form-text text-warning"><i class="bi bi-exclamation-circle me-1"></i>No se encontraron trabajadores activos. Revisa el catálogo de trabajadores activos.</div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-6">
                <label for="id_origen" class="form-label fw-semibold">Origen <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                    <input type="number" min="1" step="1" class="form-control<?= $errorCampo('id_origen') ?>" id="id_origen" name="id_origen" value="<?= escapar((string) ($anterior['id_origen'] ?? '')) ?>" placeholder="ID del origen" required>
                    <?php if (isset($errores['id_origen'])): ?><div class="invalid-feedback"><?= escapar($errores['id_origen']) ?></div><?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="id_destino" class="form-label fw-semibold">Destino <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                    <input type="number" min="1" step="1" class="form-control<?= $errorCampo('id_destino') ?>" id="id_destino" name="id_destino" value="<?= escapar((string) ($anterior['id_destino'] ?? '')) ?>" placeholder="ID del destino" required>
                    <?php if (isset($errores['id_destino'])): ?><div class="invalid-feedback"><?= escapar($errores['id_destino']) ?></div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="request-form-actions mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check2-circle me-2"></i>Registrar
        </button>
    </div>
</form>
