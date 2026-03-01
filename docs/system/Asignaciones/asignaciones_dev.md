# Asignaciones — Documentación para Desarrolladores

## Descripción General
El módulo de Asignaciones es el núcleo operativo de SIGERUTT. Vincula una ruta geográfica con un vehículo activo y un operador disponible para una fecha programada. Actúa como tabla de hechos que consolida los catálogos maestros (Rutas, Vehículos, Operadores) en una operación logística.

---

## Archivos del Módulo
| Archivo | Propósito |
|---------|-----------|
| `asignaciones.php` | Formulario de creación de nuevas asignaciones. |
| `ver_asignaciones.php` | Listado con triple JOIN: rutas, vehículos, operadores. |
| `php/asignaciones.php` | Backend de inserción de registros de asignación. |
| `php/editar_asignacion.php` | Formulario para reprogramar o modificar una asignación. |
| `php/actualizar_asignacion.php` | Procesamiento de cambios en la asignación. |
| `php/eliminar_asignacion.php` | Cancelación/eliminación de la asignación. |

---

## Lógica de Negocio y Filtros
El formulario `asignaciones.php` carga solo recursos disponibles mediante consultas SQL en la carga inicial (`GET`):

```sql
-- Solo vehículos activos
SELECT id_vehiculo, placa FROM vehiculos WHERE estado = 'activo' ORDER BY placa ASC;

-- Solo operadores disponibles
SELECT id_operador, nombre FROM operadores WHERE disponibilidad = 'disponible' ORDER BY nombre ASC;

-- Todas las rutas
SELECT id_ruta, nombre_ruta FROM rutas ORDER BY nombre_ruta ASC;
```

---

## Solicitudes HTTP del Módulo

### 1. Carga del Formulario de Asignación (GET con Filtros)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  GET /asignaciones.php                 │
       │ ─────────────────────────────────────► │
       │                                        │
       │                              ┌─────────┴────────────┐
       │                              │ Query 1:             │
       │                              │ SELECT rutas         │
       │                              │ ORDER BY nombre ASC  │
       │                              │                      │
       │                              │ Query 2:             │
       │                              │ SELECT vehiculos     │
       │                              │ WHERE estado='activo'│
       │                              │                      │
       │                              │ Query 3:             │
       │                              │ SELECT operadores    │
       │                              │ WHERE disp='disp...' │
       │                              └─────────┬────────────┘
       │                                        │
       │  ◄──────────── 200 OK ─────────────────│
       │  (HTML con 3 <select> pre-poblados:    │
       │   Rutas / Vehículos Activos /          │
       │   Operadores Disponibles)              │
```

---

### 2. Creación de Nueva Asignación (POST)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [Usuario selecciona ruta,             │
       │   vehículo, operador y fecha]          │
       │  [JS valida que ningún select          │
       │   esté en valor vacío]                 │
       │                                        │
       │  POST /php/asignaciones.php            │
       │  Content-Type: application/x-www-form-urlencoded
       │ ─────────────────────────────────────► │
       │  Body:                                 │
       │    id_ruta      = 3                    │
       │    id_vehiculo  = 7                    │
       │    id_operador  = 2                    │
       │    fecha_asignacion = "2026-03-15"     │
       │                                        │
       │                              ┌─────────┤
       │                              │ empty() │
       │                              │ check   │
       │                              │ (4 IDs) │
       │                              │         │
       │                              │ INSERT  │
       │                              │ INTO    │
       │                              │ asigna- │
       │                              │ ciones  │
       │                              │ (id_ruta│
       │                              │ id_veh  │
       │                              │ id_oper │
       │                              │ fecha)  │
       │                              └─────────┤
       │                                        │
       │  ◄─── 302 ver_asignaciones.php?exito ──│
```

**Payload POST:**

| Campo | Tipo HTML | Validación Cliente | Validación Servidor |
|-------|-----------|--------------------|---------------------|
| `id_ruta` | `select` | JS: valor !== "" | `!empty()` |
| `id_vehiculo` | `select` | JS: valor !== "" | `!empty()` |
| `id_operador` | `select` | JS: valor !== "" | `!empty()` |
| `fecha_asignacion` | `date` | `required` HTML5 | `!empty()` |

---

### 3. Edición de Asignación (GET + POST)

```
PASO 1 — Carga del formulario con datos actuales
---------------------------------------------------
  GET /php/editar_asignacion.php?id=12
         │
         ▼
  ┌──────────────────────────────────────────┐
  │ SELECT * FROM asignaciones WHERE id=12   │
  │ Re-query rutas, vehiculos activos,       │
  │ operadores disponibles para los selects  │
  └──────────────────────────────────────────┘
         │
         ▼
  200 OK (form con selects pre-seleccionados
          en los valores actuales de la asignación)


PASO 2 — Envío de actualización
---------------------------------
  POST /php/actualizar_asignacion.php
  Body: id_asignacion=12, id_ruta, id_vehiculo,
        id_operador, fecha_asignacion
         │
         ▼  empty() check
         ▼  UPDATE asignaciones SET ... WHERE id=12
         ▼
  302 → ver_asignaciones.php?mensaje=actualizado
```

---

### 4. Eliminación de Asignación (GET)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [clic "Eliminar"] → confirm()         │
       │  window.location.href =                │
       │    "php/eliminar_asignacion.php?id=12" │
       │                                        │
       │  GET /php/eliminar_asignacion.php?id=12│
       │ ─────────────────────────────────────► │
       │                              ┌─────────┤
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ asigna- │
       │                              │ ciones  │
       │                              │ WHERE   │
       │                              │ id=12   │
       │                              └─────────┤
       │  ◄── 302 ver_asignaciones?eliminado ───│
```

> **Nota**: La eliminación de una asignación **no modifica** el `estado` del vehículo ni la `disponibilidad` del operador. Esos campos deben gestionarse manualmente en sus módulos.

---

### 5. Acceso al Mapa de la Ruta Asignada (GET)

```
  Desde ver_asignaciones.php

  [clic "Ver Mapa" en fila con id_asignacion=12]
         │
         ▼
  GET /ver_mapa.php?id={id_ruta_de_la_asignacion}
         │
         ▼  SELECT * FROM rutas + paradas WHERE id_ruta=X
         ▼
  200 OK (Leaflet con ruta trazada)
```

---

## Estructura de Datos

### Tabla: `asignaciones` (Tabla de Hechos)
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_asignacion` | INT AUTO_INCREMENT | PK | Identificador único. |
| `id_ruta` | INT | FK → `rutas` | Trayecto asignado. |
| `id_vehiculo` | INT | FK → `vehiculos` | Unidad de transporte. |
| `id_operador` | INT | FK → `operadores` | Conductor responsable. |
| `fecha_asignacion` | DATE | — | Fecha programada de ejecución. |

### Diagrama Entidad-Relación
```
        ┌──────────┐
        │  rutas   │
        │ id_ruta  │
        └────┬─────┘
             │ 1
             │
             ▼ N
  ┌──────────────────────┐
  │     asignaciones     │
  ├──────────────────────┤
  │ id_asignacion     PK │
  │ id_ruta           FK │◄──────┐
  │ id_vehiculo       FK │       │
  │ id_operador       FK │       │
  │ fecha_asignacion     │       │
  └──────────────────────┘       │
             │                   │
    ┌────────┴────────┐    ┌─────┘
    ▼                 ▼    │
┌───────────┐  ┌───────────────┐
│ vehiculos │  │  operadores   │
│id_vehiculo│  │ id_operador   │
│ estado    │  │ disponibilidad│
└───────────┘  └───────────────┘
```

---

## Consulta SQL Principal del Listado
```sql
-- ver_asignaciones.php: triple JOIN para datos legibles
SELECT
    asig.id_asignacion,
    r.nombre_ruta,
    v.placa,
    o.nombre  AS nombre_operador,
    asig.fecha_asignacion
FROM asignaciones asig
JOIN rutas      r ON asig.id_ruta      = r.id_ruta
JOIN vehiculos  v ON asig.id_vehiculo  = v.id_vehiculo
JOIN operadores o ON asig.id_operador  = o.id_operador
ORDER BY asig.fecha_asignacion DESC;
```

---

## Flujo Completo de Asignación
```
 ┌──────────┐  GET asignaciones.php      ┌─────────────────────┐
 │ Usuario  │──────────────────────────► │   asignaciones.php  │
 └──────────┘                            └─────────┬───────────┘
                                                   │ Carga 3 queries
                                         ┌─────────▼───────────┐
                                         │ SELECT rutas        │
                                         │ SELECT vehiculos    │
                                         │   WHERE activo      │
                                         │ SELECT operadores   │
                                         │   WHERE disponible  │
                                         └─────────┬───────────┘
                                                   │ 200 OK (form)
                                         ┌─────────▼───────────┐
                                         │ Usuario selecciona  │
                                         │ ruta, vehículo,     │
                                         │ operador, fecha     │
                                         └─────────┬───────────┘
                                                   │ POST
                                         ┌─────────▼───────────┐
                                         │ php/asignaciones.php│
                                         ├─────────────────────┤
                                         │ 1. empty() check    │
                                         │ 2. INSERT INTO      │
                                         │    asignaciones     │
                                         └─────────┬───────────┘
                                                   │
                    ┌──────────────────────────────┴──────────┐
                    │ ?exito  ?campos_vacios  ?error_bd        │
                    └─────────────────────────────────────────┘
                                                   │
                                         ┌─────────▼───────────┐
                                         │ ver_asignaciones    │
                                         │     .php            │
                                         └─────────────────────┘
```

---

## Códigos de Mensajería
| `?mensaje=` | Significado | Destino |
|-------------|-------------|---------|
| `exito` | Asignación creada correctamente. | `ver_asignaciones.php` |
| `actualizado` | Asignación modificada. | `ver_asignaciones.php` |
| `eliminado` | Asignación cancelada. | `ver_asignaciones.php` |
| `campos_vacios` | Faltan campos requeridos. | Formulario origen |
| `error` | Fallo en INSERT/UPDATE de BD. | Formulario origen |

---

## Notas de Mantenimiento
- **Sin bloqueo de agenda**: El sistema permite múltiples asignaciones para un mismo vehículo u operador en la misma fecha. Implementar un `SELECT COUNT(*)` antes del `INSERT` si se requiere control de disponibilidad por fecha.
- **Eliminación no en cascada**: Borrar una asignación no cambia el estado de vehículo ni operador. Considerar actualizar `disponibilidad = 'disponible'` del operador al cancelar si hay lógica de negocio que lo requiera.
- **Triple JOIN**: La consulta del listado impacta en rendimiento a medida que crezca la tabla. Agregar índices en `id_ruta`, `id_vehiculo`, `id_operador` en la tabla `asignaciones`.
- **Integridad referencial**: Verificar que las restricciones `FOREIGN KEY` estén activas en MariaDB (`FOREIGN_KEY_CHECKS=1`) para garantizar que no existan asignaciones con referencias a rutas/vehículos/operadores eliminados.
