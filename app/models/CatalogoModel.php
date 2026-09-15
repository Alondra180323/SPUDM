<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class CatalogoModel extends BaseModel
{
    public function trabajadoresActivos(): array
    {
        $sql = "SELECT
                    ID_USUARIO_SPUDM AS id,
                    NOMBRE_USUARIO AS nombre,
                    APELLIDOS_USUARIO AS apellidos,
                    NUMERO_TRABAJADOR_USUARIO AS numero_trabajador,
                    NUMERO_TELEFONO_USUARIO AS telefono,
                    ID_CLIENTE_USUARIO AS id_cliente,
                    ID_AREA_USUARIO AS id_area,
                    ID_ESTATUS_USUARIO AS id_estatus
                FROM tbl_usuarios_spudm
                WHERE ID_ESTATUS_USUARIO = :id_estatus_activo
                ORDER BY NOMBRE_USUARIO ASC, APELLIDOS_USUARIO ASC";

        $consulta = $this->conexion()->prepare($sql);
        $consulta->execute([
            'id_estatus_activo' => (int) configuracion('usuarios.id_estatus_activo'),
        ]);

        return $consulta->fetchAll();
    }

    public function buscarTrabajadorActivoPorId(int $idTrabajador): ?array
    {
        $sql = "SELECT
                    ID_USUARIO_SPUDM AS id,
                    NOMBRE_USUARIO AS nombre,
                    APELLIDOS_USUARIO AS apellidos,
                    NUMERO_TRABAJADOR_USUARIO AS numero_trabajador,
                    NUMERO_TELEFONO_USUARIO AS telefono,
                    ID_CLIENTE_USUARIO AS id_cliente,
                    ID_AREA_USUARIO AS id_area,
                    ID_ESTATUS_USUARIO AS id_estatus
                FROM tbl_usuarios_spudm
                WHERE ID_USUARIO_SPUDM = :id_trabajador
                  AND ID_ESTATUS_USUARIO = :id_estatus_activo
                LIMIT 1";

        $consulta = $this->conexion()->prepare($sql);
        $consulta->execute([
            'id_trabajador' => $idTrabajador,
            'id_estatus_activo' => (int) configuracion('usuarios.id_estatus_activo'),
        ]);

        $trabajador = $consulta->fetch();
        return $trabajador ?: null;
    }
}
