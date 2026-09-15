<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CatalogoService;

final class CatalogoController extends BaseController
{
    public function trabajadoresActivos(): void
    {
        $trabajadores = (new CatalogoService())->trabajadoresActivos();

        $this->vista('catalogos.trabajadores_activos', [
            'title' => 'Trabajadores activos',
            'trabajadores' => $trabajadores,
        ]);
    }
}
