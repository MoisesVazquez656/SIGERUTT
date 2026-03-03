# Operadores — Documentación para Desarrolladores

## Descripción General
El módulo de Operadores gestiona el personal conductor del sistema SIGERUTT. Registra información de contacto y legal (licencia) de cada conductor. El campo `disponibilidad` (`disponible`/`no disponible`) es el filtro clave para el módulo de Asignaciones.

---

## Archivos del Módulo
| Archivo | Propósito |
|---------|-----------|
| `registrar_operador.php` | Formulario de alta para nuevos conductores. |
| `ver_operadores.php` | Listado y control de disponibilidad de operadores. |
| `php/registrar_operador.php` | Backend de registro, validaciones y sanitización. |
| `php/editar_operador.php` | Formulario de edición de perfil del conductor. |
| `php/actualizar_operador.php` | Procesamiento de cambios en el registro. |
| `php/eliminar_operador.php` | Remoción de operadores del sistema. |

---

## Solicitudes HTTP del Módulo

### 1. Registro de Nuevo Operador (POST)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  GET /registrar_operador.php           │
       │ ─────────────────────────────────────► │
       │  ◄──────────── 200 OK ──────────────── │
       │   (Formulario con campos vacíos)       │
       │                                        │
       │  [Usuario llena nombre, licencia,      │
       │   teléfono y disponibilidad]           │
       │  [JS: valida campos no vacíos]         │
       │                                        │
       │  POST /php/registrar_operador.php      │
       │  Content-Type: application/x-www-form-urlencoded
       │ ─────────────────────────────────────► │
       │  Body:                                 │
       │    nombre        = "Luis Hernández"    │
       │    licencia      = "HER-8901-MX"       │
       │    telefono      = "9931234567"        │
       │    disponibilidad= "disponible"        │
       │                                        │
       │                              ┌─────────┤
       │                              │ Sanitiza│
       │                              │ FILTER_ │
       │                              │ SANITIZE│
       │                              │ _STRING │
       │                              │         │
       │                              │ empty() │
       │                              │ check   │
       │                              │         │
       │                              │ SELECT  │
       │                              │ licencia│
       │                              │ dup?    │
       │                              │ LOWER() │
       │                              │         │
       │             Si duplicada:    │         │
       │  ◄── 302 ?licencia_repetida ─┤         │
       │                              │         │
       │             Si OK:           │         │
       │                              │ INSERT  │
       │                              │ INTO    │
       │                              │ operado-│
       │                              │ res     │
       │                              └─────────┤
       │  ◄──── 302 ver_operadores.php?exito ───│
```

**Payload POST:**

| Campo | Tipo HTML | Validación Cliente | Validación Servidor |
|-------|-----------|--------------------|---------------------|
| `nombre` | `text` | JS: `!empty` | `!empty()` + `FILTER_SANITIZE_STRING` |
| `licencia` | `text` | JS: `!empty` | `LOWER()` dup + `FILTER_SANITIZE_STRING` |
| `telefono` | `text` | JS: `!empty` | `!empty()` (formato libre) |
| `disponibilidad` | `select` | `required` HTML5 | `!empty()` |

---

### 2. Sanitización en Backend

```
  $_POST recibido
         │
         ▼
  ┌─────────────────────────────────────────┐
  │ $nombre = filter_input(INPUT_POST,      │
  │           'nombre',                     │
  │           FILTER_SANITIZE_STRING)       │
  │                                         │
  │ $licencia = filter_input(INPUT_POST,    │
  │             'licencia',                 │
  │             FILTER_SANITIZE_STRING)     │
  │                                         │
  │ $telefono = filter_input(INPUT_POST,    │
  │             'telefono',                 │
  │             FILTER_SANITIZE_STRING)     │
  └─────────────────────┬───────────────────┘
                        │
                        ▼
           ¿Algún campo está vacío?
          /                         \
        SÍ                          NO
         │                           │
         ▼                           ▼
  302 → ?campos_vacios       SELECT licencia (dup?)
                                     │
                             ¿Dup?  / \
                                  SÍ   NO
                                  │     │
                                  ▼     ▼
                         ?licencia_ INSERT
                          repetida  INTO
                                    operadores
```

---

### 3. Edición de Operador (GET + POST)

```
PASO 1 — Carga del formulario
──────────────────────────────
  GET /php/editar_operador.php?id=4
         │
         ▼  SELECT * FROM operadores WHERE id=4
         ▼
  200 OK (form con nombre/licencia/tel/disp precargados)


PASO 2 — Actualización
───────────────────────
  POST /php/actualizar_operador.php
  Body: id_operador=4, nombre, licencia, telefono, disponibilidad
         │
         ▼  Sanitiza campos
         ▼  Valida dup licencia (excl. self)
         ▼  UPDATE operadores SET ... WHERE id=4
         ▼
  302 → ver_operadores.php?mensaje=actualizado
```

---

### 4. Eliminación de Operador (GET)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [clic "Eliminar"] → confirm()         │
       │  window.location.href =                │
       │    "php/eliminar_operador.php?id=4"    │
       │                                        │
       │  GET /php/eliminar_operador.php?id=4   │
       │ ─────────────────────────────────────► │
       │                              ┌─────────┤
       │                              │ $_GET   │
       │                              │ ['id']  │
       │                              │         │
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ operado-│
       │                              │ res     │
       │                              │ WHERE   │
       │                              │ id=4    │
       │                              └─────────┤
       │  ◄── 302 ver_operadores.php?eliminado ─│
```

---

## Estructura de Datos

### Tabla: `operadores`
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_operador` | INT AUTO_INCREMENT | PK | Identificador único del conductor. |
| `nombre` | VARCHAR(150) | — | Nombre completo. |
| `licencia` | VARCHAR(50) | UNIQUE | Número legal de conducción. |
| `telefono` | VARCHAR(20) | — | Número de contacto (formato libre). |
| `disponibilidad` | ENUM | — | `disponible`, `no disponible`. |

### Relación con Asignaciones
```
  ┌─────────────────┐          ┌──────────────────┐
  │   operadores    │  1 : N   │   asignaciones   │
  ├─────────────────┤◄─────────├──────────────────┤
  │ id_operador  PK │          │ id_operador FK   │
  │ nombre          │          │ id_vehiculo FK   │
  │ licencia UNIQUE │          │ id_ruta FK       │
  │ telefono        │          │ fecha_asignacion │
  │ disponibilidad  │          └──────────────────┘
  └─────────────────┘
  disponibilidad='disponible'    → aparece en Asignaciones
  disponibilidad='no disponible' → oculto en Asignaciones
```

---

## Flujo Completo de Registro
```
 ┌──────────┐  nombre/licencia/tel/disp  ┌────────────────────────┐
 │ Usuario  │──────────────────────────► │ registrar_operador.php │
 └──────────┘                            └──────────┬─────────────┘
                                                    │ POST
                                         ┌──────────▼─────────────┐
                                         │ php/registrar_operador │
                                         ├────────────────────────┤
                                         │ 1. FILTER_SANITIZE     │
                                         │ 2. empty() check       │
                                         │ 3. LOWER(licencia) dup │
                                         │ 4. INSERT INTO operad. │
                                         └──────────┬─────────────┘
                                                    │
                    ┌───────────────────────────────┴──────────────┐
                    │ ?exito  ?licencia_repetida  ?campos_vacios    │
                    └──────────────────────────────────────────────┘
                                                    │
                                         ┌──────────▼─────────────┐
                                         │   ver_operadores.php   │
                                         └────────────────────────┘
```

---

## Códigos de Mensajería
| `?mensaje=` | Significado | Destino |
|-------------|-------------|---------|
| `exito` | Operador registrado correctamente. | `ver_operadores.php` |
| `actualizado` | Datos actualizados con éxito. | `ver_operadores.php` |
| `eliminado` | Operador eliminado del sistema. | `ver_operadores.php` |
| `licencia_repetida` | Licencia ya registrada. | Formulario origen |
| `campos_vacios` | Faltan campos requeridos. | Formulario origen |

---

## Notas de Mantenimiento
- **Disponibilidad**: Campo clave para Asignaciones. Mantenerlo actualizado evita asignar conductores no disponibles.
- **Formato libre**: No hay validación de formato en teléfono ni licencia. Agregar regex si se requiere interoperabilidad con otros sistemas.
- **`FILTER_SANITIZE_STRING`**: Deprecada en PHP 8.1. Migrar a `htmlspecialchars()` o validación manual con `strip_tags()`.
- **Eliminación**: Un operador con asignaciones pasadas no debería eliminarse; preferir cambiar `disponibilidad` a `no disponible` para preservar el historial logístico.
