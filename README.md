# SPUDM

Sistema de Programación de Unidades con arquitectura MVC en PHP.

Esta versión reorganiza el proyecto siguiendo la estructura modular usada en SIADMC y usa nombres propios del sistema en español.

## Módulos actuales

- Autenticación.
- Inicio por rol.
- Solicitudes.
- Catálogos.
- Catálogo de trabajadores activos.

## Trabajadores activos

El método principal es:

```php
CatalogoModel::trabajadoresActivos()
```

El estatus activo se configura en `.env`:

```env
ID_ESTATUS_USUARIO_ACTIVO=1
```

Si en tu base de datos el ID de Activo es otro, cambia únicamente ese valor.

## Estructura

Consulta `docs/ESTRUCTURA.md`.
