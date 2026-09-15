document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const entrada = document.querySelector(boton.dataset.passwordToggle);
            if (!entrada) return;

            const esPassword = entrada.type === 'password';
            entrada.type = esPassword ? 'text' : 'password';
            boton.innerHTML = `<i class="bi ${esPassword ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
            boton.setAttribute('aria-label', esPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    });

    const barraLateral = document.getElementById('sidebar');
    const botonMenu = document.getElementById('sidebarToggle');
    const fondoMenu = document.getElementById('sidebarBackdrop');

    const cerrarMenu = () => {
        barraLateral?.classList.remove('open');
        fondoMenu?.classList.remove('show');
        botonMenu?.setAttribute('aria-expanded', 'false');
    };

    const abrirMenu = () => {
        barraLateral?.classList.add('open');
        fondoMenu?.classList.add('show');
        botonMenu?.setAttribute('aria-expanded', 'true');
    };

    botonMenu?.setAttribute('aria-expanded', 'false');
    botonMenu?.addEventListener('click', () => {
        if (barraLateral?.classList.contains('open')) {
            cerrarMenu();
        } else {
            abrirMenu();
        }
    });

    fondoMenu?.addEventListener('click', cerrarMenu);

    barraLateral?.querySelectorAll('a.nav-item:not(.disabled), a.nav-subitem').forEach((enlace) => {
        enlace.addEventListener('click', () => {
            if (window.innerWidth < 992) cerrarMenu();
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) cerrarMenu();
    });
});
