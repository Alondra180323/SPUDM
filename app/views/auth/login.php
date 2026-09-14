<div class="container-fluid min-vh-100 p-0">
    <div class="row g-0 min-vh-100 flex-lg-row-reverse">
        <div class="col-lg-7 d-none d-lg-flex login-visual position-relative overflow-hidden">
            <div class="transport-lines"></div>

            <div class="transport-scene">
                <div class="scene-glow"></div>
                <div class="road-base"></div>
                <div class="road-lane lane-1"></div>
                <div class="road-lane lane-2"></div>
                <div class="city-shadow city-1"></div>
                <div class="city-shadow city-2"></div>
                <div class="city-shadow city-3"></div>

                <div class="car-animation" aria-hidden="true">
                    <div class="car-body">
                        <div class="car-top"></div>
                        <div class="car-window car-window-front"></div>
                        <div class="car-window car-window-back"></div>
                        <div class="car-light car-light-front"></div>
                        <div class="car-light car-light-back"></div>
                    </div>
                    <div class="wheel wheel-front"></div>
                    <div class="wheel wheel-back"></div>
                    <div class="car-shadow"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex align-items-center bg-white">
            <div class="login-panel w-100">
                <div class="text-center mb-4">
                    <div class="login-logo mx-auto mb-4 login-logo-plain">
                        <img src="<?= asset('img/logo.png') ?>" alt="Logo empresa" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                        <div class="logo-fallback logo-fallback-plain"><i class="bi bi-bus-front-fill"></i></div>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div><?= e($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= url('/login') ?>" autocomplete="off" class="login-form">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-semibold">Usuario</label>
                        <div class="input-group input-group-lg login-input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= e($oldUser ?? '') ?>" placeholder="Número de trabajador, correo o nombre" maxlength="80" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group input-group-lg login-input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                            <button class="btn password-toggle" type="button" data-password-toggle="#password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 login-button">
                        Iniciar sesión <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </form>

                <div class="text-center text-secondary small mt-4">© <?= date('Y') ?> Transportes</div>
            </div>
        </div>
    </div>
</div>
