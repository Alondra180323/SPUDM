# Estructura SPUDM

SPUDM conserva MVC, pero la lógica propia del sistema usa nombres en español.

```text
SPUDM/
├── app/
│   ├── controllers/
│   │   ├── AutenticacionController.php
│   │   ├── CatalogoController.php
│   │   ├── InicioController.php
│   │   └── SolicitudController.php
│   ├── core/
│   │   ├── ApiAutenticacion.php
│   │   ├── Autenticacion.php
│   │   ├── BaseController.php
│   │   ├── BaseModel.php
│   │   ├── BaseService.php
│   │   ├── Conexion.php
│   │   ├── Csrf.php
│   │   ├── Enrutador.php
│   │   ├── Entorno.php
│   │   ├── ManejadorApiBase.php
│   │   ├── no_cache.php
│   │   └── plantilla.php
│   ├── helpers/
│   ├── middleware/
│   ├── models/
│   │   ├── CatalogoModel.php
│   │   ├── SolicitudModel.php
│   │   └── UsuarioModel.php
│   ├── services/
│   │   ├── AutenticacionService.php
│   │   ├── CatalogoService.php
│   │   └── SolicitudService.php
│   └── views/
│       ├── autenticacion/
│       ├── catalogos/
│       ├── errores/
│       ├── inicio/
│       ├── plantillas/
│       └── solicitudes/
├── public/
│   ├── api/
│   │   └── catalogos/
│   │       └── trabajadores_activos.php
│   └── assets/
│       ├── css/
│       ├── img/
│       └── js/
│           ├── axios/
│           │   ├── catalogos/
│           │   └── solicitudes/
│           └── core/
└── docs/
```

## Funciones en español

Ejemplos usados en el sistema:

- `trabajadoresActivos()`
- `buscarTrabajadorActivoPorId()`
- `registrarSolicitud()`
- `solicitudesRecientes()`
- `pendientesParaAceptar()`
- `conteosInicio()`
- `iniciarSesion()`
- `cerrarSesion()`

## Catálogo de trabajadores activos

El origen de datos está en:

`app/models/CatalogoModel.php`

El filtro actual es:

```sql
WHERE ID_ESTATUS_USUARIO = :id_estatus_activo
```

El ID se configura en `.env`:

```env
ID_ESTATUS_USUARIO_ACTIVO=1
```

La vista se abre en:

`/catalogos/trabajadores-activos`

El endpoint para Select2/Axios es:

`/api/catalogos/trabajadores_activos.php`
