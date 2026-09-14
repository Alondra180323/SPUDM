<?php $user = auth_user(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'SPUDM') ?> | SPUDM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <div>
                <div class="brand-name">SPUDM</div>
                <div class="brand-subtitle">Gestión de programación</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item active" href="<?= url('/dashboard') ?>"><i class="bi bi-grid-1x2-fill"></i><span>Inicio</span></a>
            <?php if (can('solicitudes.ver')): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-file-earmark-text-fill"></i><span>Solicitudes</span></a>
            <?php endif; ?>
            <?php if (can('programacion.ver')): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-calendar2-check-fill"></i><span>Programación</span></a>
            <?php endif; ?>
            <?php if (can('vehiculos.ver')): ?>
                <a class="nav-item disabled" href="#" title="Próximo módulo"><i class="bi bi-bus-front-fill"></i><span>Vehículos</span></a>
            <?php endif; ?>
            <?php if (($user['rol_slug'] ?? '') === 'administrador'): ?>
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
            <button class="btn btn-light d-lg-none shadow-sm" id="sidebarToggle" type="button"><i class="bi bi-list"></i></button>
            <div class="topbar-title d-none d-md-block">
                <div class="fw-semibold">Sistema de Programación de Unidades</div>
                <small class="text-secondary">Transportes · Control operativo</small>
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="user-chip">
                    <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold lh-1"><?= e($user['nombre'] ?? 'Usuario') ?></div>
                        <small class="text-secondary"><?= e($user['rol'] ?? '') ?></small>
                    </div>
                </div>
                <form method="post" action="<?= url('/logout') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-primary btn-sm px-3" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Salir</button>
                </form>
            </div>
        </header>

        <section class="content-area">
            <?= $content ?>
        </section>
    </main>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
