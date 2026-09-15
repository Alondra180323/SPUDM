<?php
$usuario = usuario_autenticado();
$rutaUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$rutaBase = rtrim((string) parse_url((string) configuracion('app.url'), PHP_URL_PATH), '/');
$rutaActual = $rutaBase !== '' && str_starts_with($rutaUri, $rutaBase)
    ? substr($rutaUri, strlen($rutaBase)) ?: '/'
    : $rutaUri;

$esInicio = rtrim($rutaActual, '/') === '/dashboard';
$esSolicitudes = str_starts_with($rutaActual, '/solicitudes');
$esSolicitudRegistrar = rtrim($rutaActual, '/') === '/solicitudes/nueva';
$esSolicitudVer = rtrim($rutaActual, '/') === '/solicitudes';
$esSolicitudAceptar = rtrim($rutaActual, '/') === '/solicitudes/aceptar';
$esCatalogos = str_starts_with($rutaActual, '/catalogos');
$esTrabajadoresActivos = rtrim($rutaActual, '/') === '/catalogos/trabajadores-activos';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'SPUDM') ?> | SPUDM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="app-body">
<div class="spudm-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-wrap">
                <img src="<?= asset('img/logo.png') ?>" alt="Logo empresa" class="brand-logo" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                <div class="brand-logo-fallback"><i class="bi bi-bus-front-fill"></i></div>
            </div>
            <div class="brand-copy flex-grow-1 min-w-0">
                <div class="brand-name-row">
                    <div class="brand-name">SPUDM</div>
                    <button type="button" class="brand-notification-btn" title="Notificaciones" aria-label="Notificaciones">
                        <i class="bi bi-bell-fill"></i>
                        <span class="notification-badge">0</span>
                    </button>
                </div>
                <div class="brand-subtitle">Gestión de programación</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item <?= $esInicio ? 'active' : '' ?>" href="<?= url('/dashboard') ?>">
                <i class="bi bi-grid-1x2-fill"></i><span>Inicio</span>
            </a>

            <?php if (tiene_permiso('solicitudes.ver') || tiene_permiso('solicitudes.crear') || tiene_permiso('solicitudes.validar')): ?>
                <button class="nav-item nav-parent <?= $esSolicitudes ? 'active' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#solicitudSubmenu" aria-expanded="<?= $esSolicitudes ? 'true' : 'false' ?>" aria-controls="solicitudSubmenu">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Solicitud</span>
                    <i class="bi bi-chevron-down nav-chevron ms-auto"></i>
                </button>
                <div class="collapse nav-submenu <?= $esSolicitudes ? 'show' : '' ?>" id="solicitudSubmenu">
                    <?php if (tiene_permiso('solicitudes.crear')): ?>
                        <a class="nav-subitem <?= $esSolicitudRegistrar ? 'active' : '' ?>" href="<?= url('/solicitudes/nueva') ?>"><i class="bi bi-plus-circle-fill"></i><span>Registrar solicitud</span></a>
                    <?php endif; ?>
                    <?php if (tiene_permiso('solicitudes.ver')): ?>
                        <a class="nav-subitem <?= $esSolicitudVer ? 'active' : '' ?>" href="<?= url('/solicitudes') ?>"><i class="bi bi-list-ul"></i><span>Ver solicitudes</span></a>
                    <?php endif; ?>
                    <?php if (tiene_permiso('solicitudes.validar')): ?>
                        <a class="nav-subitem <?= $esSolicitudAceptar ? 'active' : '' ?>" href="<?= url('/solicitudes/aceptar') ?>"><i class="bi bi-check2-square"></i><span>Aceptar solicitudes</span></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (tiene_permiso('catalogos.ver')): ?>
                <button class="nav-item nav-parent <?= $esCatalogos ? 'active' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#catalogosSubmenu" aria-expanded="<?= $esCatalogos ? 'true' : 'false' ?>" aria-controls="catalogosSubmenu">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Catálogos</span>
                    <i class="bi bi-chevron-down nav-chevron ms-auto"></i>
                </button>
                <div class="collapse nav-submenu <?= $esCatalogos ? 'show' : '' ?>" id="catalogosSubmenu">
                    <a class="nav-subitem <?= $esTrabajadoresActivos ? 'active' : '' ?>" href="<?= url('/catalogos/trabajadores-activos') ?>">
                        <i class="bi bi-people-fill"></i><span>Trabajadores activos</span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (tiene_permiso('programacion.ver')): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-calendar2-check-fill"></i><span>Programación</span></a>
            <?php endif; ?>
            <?php if (tiene_permiso('vehiculos.ver')): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-bus-front-fill"></i><span>Vehículos</span></a>
            <?php endif; ?>
            <?php if (($usuario['rol_slug'] ?? '') === 'administrador'): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-shield-lock-fill"></i><span>Usuarios y permisos</span></a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="small opacity-75">Sistema de Programación de Unidades</div>
            <div class="small opacity-50">SPUDM</div>
        </div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <button class="btn btn-light d-lg-none shadow-sm menu-toggle" id="sidebarToggle" type="button" aria-label="Abrir menú"><i class="bi bi-list"></i></button>
            <div class="topbar-title d-none d-md-block">
                <div class="fw-semibold">Sistema de Programación de Unidades</div>
                <small class="text-secondary">Transportes · Control operativo</small>
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="user-chip">
                    <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
                    <div class="d-none d-sm-block user-copy">
                        <div class="fw-semibold lh-1 text-truncate"><?= e($usuario['nombre'] ?? 'Usuario') ?></div>
                        <small class="text-secondary"><?= e($usuario['rol'] ?? '') ?></small>
                    </div>
                </div>
                <form method="post" action="<?= url('/logout') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button class="btn logout-icon-btn" type="submit" title="Cerrar sesión" aria-label="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </header>

        <section class="content-area">
            <?= $contenido ?>
        </section>
    </main>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<script>window.SPUDM_URL = <?= json_encode(rtrim((string) configuracion('app.url'), '/'), JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= asset('js/core/configuracion.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/axios/catalogos/trabajadores_activos.js') ?>"></script>
</body>
</html>
