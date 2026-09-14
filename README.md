# SPUDM — Starter MVC

Base inicial para **SPUDM (Sistema de Programación de Unidades)** orientado a una empresa de transportes.

## Qué incluye

- Arquitectura MVC sin exponer `/app` al navegador.
- `public/` como DocumentRoot y Front Controller.
- Login con usuario y contraseña.
- `password_verify()` para contraseñas seguras.
- Protección CSRF.
- Sesiones con cookie HttpOnly / SameSite=Lax.
- Roles: `administrador`, `supervisor`, `laborales`, `programacion`.
- Permisos configurables por rol.
- Dashboard distinto por rol.
- Diseño empresarial azul rey + blanco, responsive y orientado a transporte.
- Logo esperado en `public/assets/img/logo.png`.

## Estructura

```text
SPUDM/
├─ app/
│  ├─ config/
│  ├─ controllers/
│  ├─ core/
│  ├─ helpers/
│  ├─ middleware/
│  ├─ models/
│  ├─ services/
│  └─ views/
├─ public/
│  ├─ assets/
│  ├─ .htaccess
│  └─ index.php
├─ storage/logs/
├─ .env.example
└─ README.md
```

## Instalación rápida

1. Copia el proyecto en tu servidor local.
2. Configura Apache para que el **DocumentRoot apunte a `SPUDM/public`**.
3. Copia `.env.example` como `.env` y coloca los datos reales de tu BD.
4. Copia el logo de la empresa a:
   `public/assets/img/logo.png`
5. Ajusta **únicamente** la consulta de `app/models/UserModel.php` a tus tablas reales.
6. Asegúrate de guardar las contraseñas con `password_hash()`.

## Consulta que espera el login

El ejemplo actual supone:

- `tbl_usuarios.id_usuario`
- `tbl_usuarios.usuario`
- `tbl_usuarios.password`
- `tbl_usuarios.nombre`
- `tbl_usuarios.id_rol`
- `tbl_usuarios.activo`
- `tbl_roles.id_rol`
- `tbl_roles.nombre`

Si tu BD usa otros nombres, cambia solo `UserModel::findByUsername()`.

## Roles reconocidos

- Administrador
- Supervisor
- Laborales
- Programación

El nombre del rol se normaliza internamente a:

- `administrador`
- `supervisor`
- `laborales`
- `programacion`

## Siguiente etapa recomendada

1. Catálogo de usuarios y permisos.
2. Módulo de solicitudes.
3. Flujo Supervisor → Laborales → Programación.
4. Catálogo de vehículos/unidades.
5. Programación y asignación.
6. Histórico, estatus y trazabilidad.
7. Dashboard con métricas reales.

## Login conectado a tbl_usuarios_spudm

El login ya consulta directamente `tbl_usuarios_spudm`.

El campo Usuario acepta actualmente:
- `NUMERO_TRABAJADOR_USUARIO`
- `EMAIL_USUARIO`
- `NOMBRE_USUARIO`

La contraseña se compara contra `PASSWORD_USUARIO`. Si fue insertada temporalmente en texto plano, al primer inicio correcto se convierte automáticamente usando `password_hash()`.

Asignación temporal de roles hasta conectar la tabla real de roles:
- 1 = Administrador
- 2 = Supervisor
- 3 = Laborales
- 4 = Programación

Si `ID_ROL_USUARIO` contiene otro valor, por seguridad se usa el perfil Supervisor de manera temporal.
