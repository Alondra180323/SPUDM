(() => {
    'use strict';

    window.SPUDM = window.SPUDM || {};
    window.SPUDM.urlBase = (window.SPUDM_URL || '').replace(/\/$/, '');

    window.SPUDM.ruta = (ruta = '') => {
        const limpia = String(ruta).replace(/^\//, '');
        return `${window.SPUDM.urlBase}/${limpia}`;
    };
})();
