<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\BaseService;
use App\Models\CatalogoModel;

final class CatalogoService extends BaseService
{
    public function __construct(private readonly CatalogoModel $catalogos = new CatalogoModel()) {}

    public function trabajadoresActivos(): array
    {
        return $this->catalogos->trabajadoresActivos();
    }

    public function buscarTrabajadorActivoPorId(int $idTrabajador): ?array
    {
        return $this->catalogos->buscarTrabajadorActivoPorId($idTrabajador);
    }
}
