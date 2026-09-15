<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class BaseModel
{
    protected function conexion(): PDO
    {
        return Conexion::obtener();
    }
}
