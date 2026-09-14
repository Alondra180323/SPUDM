document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.passwordToggle);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.innerHTML = `<i class="bi ${isPassword ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
            button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    });

    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        backdrop?.classList.remove('show');
        toggle?.setAttribute('aria-expanded', 'false');
    };

    const openSidebar = () => {
        sidebar?.classList.add('open');
        backdrop?.classList.add('show');
        toggle?.setAttribute('aria-expanded', 'true');
    };

    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.addEventListener('click', () => {
        if (sidebar?.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    backdrop?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('a.nav-item:not(.disabled), a.nav-subitem').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) closeSidebar();
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) closeSidebar();
    });

    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.select2-worker').select2({
            width: '100%',
            placeholder: function () {
                return jQuery(this).data('placeholder') || 'Selecciona...';
            },
            allowClear: true,
            language: {
                noResults: () => 'No se encontraron trabajadores',
                searching: () => 'Buscando...'
            }
        });
    }
});
