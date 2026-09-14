document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.passwordToggle);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.innerHTML = `<i class="bi ${isPassword ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
        });
    });

    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        backdrop?.classList.remove('show');
    };

    toggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        backdrop?.classList.toggle('show');
    });

    backdrop?.addEventListener('click', closeSidebar);
});
