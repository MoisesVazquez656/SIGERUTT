# Usuarios — Documentación para Desarrolladores

## Descripción General
Módulo encargado de la gestión integral de los usuarios del sistema SIGERUTT. Implementa control de acceso basado en roles (RBAC simplificado) con tres perfiles: **Administrador**, **Supervisor** y **Operador**. Garantiza la integridad de los datos de contacto y autenticación mediante hashing de contraseñas y verificación de unicidad.

---

## Archivos del Módulo
| Archivo | Propósito |
|---------|-----------|
| `registrar_usuario.php` | Formulario de registro de nuevos usuarios (Frontend). |
| `ver_usuarios.php` | Listado general de usuarios con acciones CRUD (Frontend). |
| `php/registrar_usuario.php` | Procesamiento del registro, validaciones y hash (Backend). |
| `php/editar_usuario.php` | Formulario de edición con datos precargados (Frontend). |
| `php/actualizar_usuario.php` | Procesamiento de la actualización de datos (Backend). |
| `php/eliminar_usuario.php` | Eliminación física del usuario de la base de datos (Backend). |
| `helpers.php` | Centraliza `require_login()`, `require_admin()` y validación de email. |

---

## Servicios y Lógica Backend

### Validaciones Críticas
1. **Campos Obligatorios**: Todos los campos deben estar presentes en el `$_POST`; se verifica con `empty()`.
2. **Email**: Validación de formato mediante `filter_var($email, FILTER_VALIDATE_EMAIL)` en `helpers.php`.
3. **Contraseña**: Mínimo 6 caracteres; almacenada con `password_hash($pass, PASSWORD_DEFAULT)`.
4. **Duplicidad**: Query `SELECT` previo verifica que ni el `nombre` ni el `correo` ya existan (`LOWER()` para comparación case-insensitive).

### Guards de Seguridad
```
  Cada archivo PHP del módulo inicia con:
  ┌────────────────────────────────────┐
  │ require_once '../helpers.php';     │
  │ require_login();    ← Verifica     │
  │                       sesión activa│
  │ require_admin();    ← Verifica rol │
  │                       = 'admin'    │
  └────────────────────────────────────┘
  Si falla → header("Location: login.php")
```

---

## Solicitudes HTTP del Módulo

### 1. Registro de Nuevo Usuario (POST)
Flujo completo desde `registrar_usuario.php` hasta el backend.

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  GET /registrar_usuario.php            │
       │ ─────────────────────────────────────► │
       │  ◄───────────── 200 OK ─────────────── │
       │   (Formulario HTML con #formUsuario)   │
       │                                        │
       │  [Usuario llena el formulario]         │
       │  [JS valida longitud contraseña >= 6]  │
       │                                        │
       │  POST /php/registrar_usuario.php       │
       │  Content-Type: application/x-www-form-urlencoded
       │ ─────────────────────────────────────► │
       │  Body:                                 │
       │    nombre     = "Juan García"          │
       │    correo     = "juan@empresa.com"     │
       │    contraseña = "pass123"              │
       │    rol        = "supervisor"           │
       │                                        │
       │                              ┌─────────┤
       │                              │ Valida  │
       │                              │ campos  │
       │                              │ empty() │
       │                              │         │
       │                              │ Valida  │
       │                              │ email   │
       │                              │ FILTER_ │
       │                              │ VALIDATE│
       │                              │         │
       │                              │ Verifica│
       │                              │ len     │
       │                              │ pass>=6 │
       │                              │         │
       │                              │ SELECT  │
       │                              │ nombre/ │
       │                              │ correo  │
       │                              │ (dup?)  │
       │                              │         │
       │                 Si duplicado:│         │
       │  ◄─── 302 ?mensaje=duplicado ┤         │
       │                              │         │
       │                 Si OK:       │         │
       │                              │ hash()  │
       │                              │ INSERT  │
       │                              │ INTO    │
       │                              │ usuarios│
       │                              └─────────┤
       │  ◄──── 302 Location: ver_usuarios.php ─│
       │              ?mensaje=exito            │
```

**Payload del POST — Detalle de campos:**

| Campo | Tipo HTML | Validación Cliente | Validación Server |
|-------|-----------|--------------------|-------------------|
| `nombre` | `text` | `required` HTML5 | `!empty()` |
| `correo` | `email` | Formato email HTML5 | `FILTER_VALIDATE_EMAIL` |
| `contraseña` | `password` | JS: `length >= 6` | `strlen() >= 6` |
| `rol` | `select` | `required` HTML5 | `!empty()` |

---

### 2. Edición de Usuario (GET + POST)

```
PASO 1 — Carga del formulario con datos actuales
─────────────────────────────────────────────────
Cliente                                Servidor
   │                                      │
   │  GET /php/editar_usuario.php?id=3    │
   │ ────────────────────────────────────►│
   │                              ┌───────┤
   │                              │SELECT │
   │                              │FROM   │
   │                              │usuario│
   │                              │WHERE  │
   │                              │id=3   │
   │                              └───────┤
   │  ◄───── 200 OK (form prefilled) ─────│
   │  (Nombre, correo y rol precargados)  │


PASO 2 — Envío de la actualización
───────────────────────────────────
Cliente                                Servidor
   │                                      │
   │  POST /php/actualizar_usuario.php    │
   │ ────────────────────────────────────►│
   │  Body:                               │
   │    id_usuario = 3                    │
   │    nombre     = "Juan García López"  │
   │    correo     = "juan@empresa.com"   │
   │    rol        = "admin"              │
   │                              ┌───────┤
   │                              │Valida │
   │                              │dup    │
   │                              │(excl. │
   │                              │self)  │
   │                              │UPDATE │
   │                              │SET    │
   │                              │WHERE  │
   │                              │id=3   │
   │                              └───────┤
   │  ◄──── 302 Location: ver_usuarios ───│
```

> **Nota**: La edición de usuario **no modifica la contraseña**. Es un campo omitido del formulario de edición. Para cambio de contraseña se requiere un flujo separado.

---

### 3. Eliminación de Usuario (GET con parámetro)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [Usuario clic en "Eliminar"]          │
       │  JS: confirm("¿Eliminar usuario?")     │
       │      → true                            │
       │  window.location.href =                │
       │    "php/eliminar_usuario.php?id=3"     │
       │                                        │
       │  GET /php/eliminar_usuario.php?id=3    │
       │ ─────────────────────────────────────► │
       │                                        │
       │                              ┌─────────┤
       │                              │ $_GET   │
       │                              │ ['id']  │
       │                              │         │
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ usuarios│
       │                              │ WHERE   │
       │                              │ id=3    │
       │                              └─────────┤
       │                                        │
       │  ◄──── 302 Location: ver_usuarios.php ─│
       │              ?mensaje=eliminado        │
```

---

### 4. Autenticación de Sesión (Guards)
Aplicado en cada carga de página protegida del módulo.

```
  Petición GET/POST a cualquier página del módulo
                        │
                        ▼
             ┌──────────────────┐
             │  require_login() │
             │  (helpers.php)   │
             └────────┬─────────┘
                      │
          ¿session_start() activa?
         /                        \
       NO                         SÍ
        │                          │
        ▼                          ▼
  header(Location:        ¿require_admin()?
   login.php)             /               \
   exit()               SÍ (rol = admin)  NO
                          │                │
                          ▼                ▼
                    Continúa          header(Location:
                    ejecución          sistemap.php)
                    normal             exit()
```

---

## Estructura de Datos

### Tabla: `usuarios`
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_usuario` | INT AUTO_INCREMENT | PK | Identificador único. |
| `nombre` | VARCHAR(150) | — | Nombre completo del usuario. |
| `correo` | VARCHAR(100) | UNIQUE | Email de acceso al sistema. |
| `contraseña` | VARCHAR(255) | — | Hash Bcrypt (PASSWORD_DEFAULT). |
| `rol` | ENUM | — | `admin`, `supervisor`, `operador`. |

### Diagrama Entidad-Relación (Contexto)
```
  ┌──────────────┐          ┌──────────────┐
  │   usuarios   │          │ asignaciones │
  ├──────────────┤          ├──────────────┤
  │ id_usuario PK│          │ id_asignacion│
  │ nombre       │          │ ...          │
  │ correo UNIQUE│          │ [creado_por  │
  │ contraseña   │          │  → futuro]   │
  │ rol ENUM     │          └──────────────┘
  └──────────────┘
  (No hay FK directa actualmente hacia
   otras tablas del sistema)
```

---

## Flujo de Datos Completo (Registro)
```
 ┌──────────┐   llena formulario  ┌──────────────────────┐
 │ Admin    │────────────────────►│ registrar_usuario.php│
 └──────────┘                     └──────────┬───────────┘
                                              │ POST submit
                                              ▼
                               ┌──────────────────────────┐
                               │  php/registrar_usuario   │
                               ├──────────────────────────┤
                               │ 1. require_login()       │
                               │ 2. empty() check         │
                               │ 3. FILTER_VALIDATE_EMAIL │
                               │ 4. strlen(pass) >= 6     │
                               │ 5. SELECT dup (nombre +  │
                               │    correo) LOWER()       │
                               │ 6. password_hash()       │
                               │ 7. INSERT INTO usuarios  │
                               └──────────┬───────────────┘
                                          │ redirect
                    ┌─────────────────────┴──────────────────┐
                    │ ?mensaje=exito                          │
                    │ ?mensaje=duplicado                      │
                    │ ?mensaje=pass_corta                     │
                    │ ?mensaje=email_invalido                 │
                    │ ?mensaje=campos_vacios                  │
                    └────────────────────────────────────────┘
                                          │
                                          ▼
                                 ┌─────────────────┐
                                 │  ver_usuarios   │
                                 │     .php        │
                                 └─────────────────┘
```

---

## Permisos Requeridos
| Rol | Acción permitida |
|-----|-----------------|
| `admin` | Acceso total: Registrar, Ver, Editar, Eliminar usuarios. |
| `supervisor` | Sin acceso al módulo (redirigido a `sistemap.php`). |
| `operador` | Sin acceso al módulo (redirigido a `sistemap.php`). |

---

## Elementos de Pantalla
| Elemento | Icono | ID/Selector | Acción |
|----------|-------|-------------|--------|
| Botón Registrar | `<i class="fa-solid fa-user-plus">` | `#formUsuario` | POST a `php/registrar_usuario.php`. |
| Enlace Editar | (Texto) | `.accion-editar` | GET a `php/editar_usuario.php?id=X`. |
| Enlace Eliminar | (Texto) | `.accion-eliminar` | GET a `php/eliminar_usuario.php?id=X`. |

---

## Códigos de Respuesta y Parámetros de Mensajería
| Parámetro GET (`?mensaje=`) | Significado | Pantalla destino |
|-----------------------------|-------------|-----------------|
| `exito` | Usuario registrado correctamente. | `ver_usuarios.php` |
| `actualizado` | Datos del usuario actualizados. | `ver_usuarios.php` |
| `eliminado` | Usuario eliminado del sistema. | `ver_usuarios.php` |
| `duplicado` | Nombre o correo ya registrados. | Formulario origen |
| `pass_corta` | Contraseña menor a 6 caracteres. | Formulario origen |
| `email_invalido` | Formato de email incorrecto. | Formulario origen |
| `campos_vacios` | Uno o más campos requeridos vacíos. | Formulario origen |

---

## Notas de Mantenimiento
- **Contraseñas**: Gestionadas con `PASSWORD_DEFAULT` (Bcrypt en PHP >= 5.5). El hash resultante es de ~60 chars; el campo `contraseña` debe ser `VARCHAR(255)` para compatibilidad futura.
- **Eliminación Física**: La eliminación es un `DELETE` directo. No existe borrado lógico (soft delete). Si el usuario tiene registros relacionados, se generará error de FK constraint si existen relaciones futuras.
- **`helpers.php`**: Contiene las funciones centralizadas `require_login()` y `require_admin()`. Modificar con cuidado ya que afecta a todos los módulos.
- **Sin rate limiting**: El formulario de login/registro no tiene protección contra fuerza bruta. Considerar implementar `$_SESSION['intentos']` o integrar CAPTCHA en versiones futuras.
