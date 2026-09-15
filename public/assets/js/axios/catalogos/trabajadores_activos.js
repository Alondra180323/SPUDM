document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const $selectTrabajador = window.jQuery ? jQuery('.select2-trabajador') : null;
    if (!$selectTrabajador || !$selectTrabajador.length || !jQuery.fn.select2) {
        return;
    }

    $selectTrabajador.select2({
        width: '100%',
        placeholder: function () {
            return jQuery(this).data('placeholder') || 'Selecciona un trabajador...';
        },
        allowClear: true,
        language: {
            noResults: () => 'No se encontraron trabajadores activos',
            searching: () => 'Buscando...'
        }
    });

    const cargarTrabajadoresActivos = async () => {
        const select = $selectTrabajador.get(0);
        if (!select || select.dataset.cargarTrabajadores !== '1' || !window.axios || !window.SPUDM) {
            return;
        }

        const valorSeleccionado = String($selectTrabajador.val() || '');

        try {
            const respuesta = await axios.get(
                window.SPUDM.ruta('api/catalogos/trabajadores_activos.php'),
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );

            if (!respuesta.data?.correcto || !Array.isArray(respuesta.data.trabajadores)) {
                return;
            }

            $selectTrabajador.empty().append(new Option('', '', false, false));

            respuesta.data.trabajadores.forEach((trabajador) => {
                const id = String(trabajador.id ?? '');
                const opcion = new Option(
                    trabajador.texto || trabajador.nombre || `Trabajador ${id}`,
                    id,
                    false,
                    id === valorSeleccionado
                );
                $selectTrabajador.append(opcion);
            });

            $selectTrabajador.trigger('change.select2');
        } catch (error) {
            console.error('No fue posible cargar los trabajadores activos.', error);
        }
    };

    cargarTrabajadoresActivos();
});
