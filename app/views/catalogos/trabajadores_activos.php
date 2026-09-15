<div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">CATÁLOGOS</span>
        <h1 class="h3 mb-1">Trabajadores activos</h1>
        <p class="text-secondary mb-0">Usuarios disponibles para asignar a las solicitudes de viaje.</p>
    </div>
    <span class="role-badge"><i class="bi bi-people-fill"></i> <?= count($trabajadores) ?> activos</span>
</div>

<div class="panel-card">
    <?php if (empty($trabajadores)): ?>
        <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No se encontraron trabajadores con el estatus activo configurado.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número de trabajador</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Cliente</th>
                        <th>Área</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trabajadores as $indice => $trabajador): ?>
                        <tr>
                            <td><?= (int) $indice + 1 ?></td>
                            <td><?= escapar($trabajador['numero_trabajador'] ?? '') ?></td>
                            <td><?= escapar(trim(($trabajador['nombre'] ?? '') . ' ' . ($trabajador['apellidos'] ?? ''))) ?></td>
                            <td><?= escapar($trabajador['telefono'] ?? '') ?></td>
                            <td><?= escapar($trabajador['id_cliente'] ?? '') ?></td>
                            <td><?= escapar($trabajador['id_area'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
