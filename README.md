# SPUDM v7 - Registro conectado a `tbl_solicitud_spudm`

Esta versión usa la tabla real proporcionada:

- `tbl_solicitud_spudm`
- `tbl_usuarios_spudm`

## Qué guarda el formulario

Al registrar una solicitud:

- `FOLIO_SOLICITUD`: se genera automáticamente.
- `FOLIO_VIAJE`: `NULL` hasta que exista un viaje programado.
- `ID_TIPO_MOVIMIENTO`: `NULL` por ahora; todavía no se definió su catálogo.
- `ID_TIPO_PROGRAMA`: Único/Fijo, mediante IDs configurables en `.env`.
- `ID_CLIENTE`: se toma del usuario que inició sesión.
- `ID_AREA`: se toma del usuario que inició sesión.
- `ID_TRABAJADOR`: trabajador activo seleccionado en Select2.
- `ID_ORIGEN`: valor capturado en Origen.
- `ID_DESTINO`: valor capturado en Destino.
- `ID_ESTATUS_SOLICITUD`: Pendiente al crear.
- `unidad_asignada`: `NULL` al crear.
- `ID_USUARIO_LABORALES`: `NULL` al crear.
- `ID_USUARIO_PROGRAMACION`: `NULL` al crear.
- `ID_USUARIO_SOLICITANTE`: usuario autenticado.
- `FECHA_CREACION_CREACION`: fecha actual.
- `HORA_CREACION_SOLICITUD`: hora actual.

Los campos de entrada/salida, vencimiento y cancelación permanecen `NULL` hasta la etapa correspondiente.

## Configuración de IDs

Copia `.env.example` a `.env` y confirma los IDs reales:

```env
ACTIVE_USER_STATUS_ID=1

SOLICITUD_STATUS_PENDIENTE_ID=1
SOLICITUD_STATUS_ESPERA_ID=2
SOLICITUD_STATUS_PROGRAMADA_ID=3
SOLICITUD_STATUS_REALIZADA_ID=4
SOLICITUD_STATUS_CANCELADA_ID=5
SOLICITUD_STATUS_VENCIDA_ID=6

SOLICITUD_TIPO_PROGRAMA_UNICO_ID=1
SOLICITUD_TIPO_PROGRAMA_FIJO_ID=2
```

Si tus catálogos usan otros IDs, cambia solamente estos valores.

## ID_SOLICITUD

La definición compartida no muestra `PRIMARY KEY AUTO_INCREMENT` en `ID_SOLICITUD`.
El código incluye compatibilidad temporal para calcular el siguiente ID, pero para producción es recomendable ejecutar:

`database/03_ajustar_id_solicitud.sql`

## Origen y Destino

La tabla define `ID_ORIGEN` e `ID_DESTINO` como `INT`. Por eso esta versión captura IDs numéricos.
Cuando se proporcionen las tablas/catálogos reales de origen y destino, estos dos campos pueden convertirse a Select2 mostrando nombres y guardando sus IDs.
