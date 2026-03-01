# Rutas — Documentación para Desarrolladores

## Descripción General
El módulo de Rutas permite la definición geográfica de trayectos mediante un mapa interactivo. Gestiona puntos de origen, destino y múltiples paradas intermedias, almacenando coordenadas precisas para su posterior asignación a vehículos. Es la base de datos geoespacial del sistema SIGERUTT.

---

## Archivos del Módulo
| Archivo | Propósito |
|---------|-----------|
| `registrar_ruta_dinamica.php` | Interfaz de creación con mapa interactivo (Leaflet). |
| `ver_rutas.php` | Listado de rutas registradas con acciones de gestión. |
| `ver_mapa.php` | Visualización de una ruta específica en el mapa. |
| `editar_ruta_dinamica.php` | Modificación de rutas existentes (nombre y coordenadas). |
| `php/registrar_ruta_dinamica.php` | Backend: almacenamiento de ruta y sus paradas asociadas. |
| `php/actualizar_ruta_dinamica.php` | Backend: actualización de datos y coordenadas. |
| `php/eliminar_ruta.php` | Backend: eliminación de la ruta y limpieza de paradas. |

---

## Integración con Mapas
Se utiliza la librería **Leaflet 1.9.4** con capas de **OpenStreetMap** (CDN externo).

- **Punto de Inicio**: Centrado por defecto en Villahermosa, Tabasco `[17.9895, -92.9256]` con zoom 13.
- **Lógica de Marcadores** (definida por el orden secuencial de clics):
  1. Primer clic → Define el **Origen** (marcador azul, label "Origen").
  2. Segundo clic → Define el **Destino** (marcador azul, label "Destino").
  3. Clics posteriores → Definen **Paradas** intermedias numeradas.
- Los campos `readonly` del formulario se actualizan automáticamente con cada clic vía JavaScript.

---

## Solicitudes HTTP del Módulo

### 1. Carga de Tiles del Mapa (OpenStreetMap)
Solicitudes externas realizadas automáticamente por Leaflet al cargar la vista.

```
Cliente (Navegador)                   Servidor OpenStreetMap
       │                                        │
       │  GET https://tile.openstreetmap.org/   │
       │      /{z}/{x}/{y}.png                  │
       │ ─────────────────────────────────────► │
       │                                        │
       │  ◄───────────── 200 OK ─────────────── │
       │       (Imagen PNG del tile del mapa)   │
       │                                        │
       │  [Leaflet repite esta petición por     │
       │   cada tile visible en pantalla]       │
```

---

### 2. Registro de Nueva Ruta (POST)
Flujo completo desde el formulario en `registrar_ruta_dinamica.php`.

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [Usuario hace clics en el mapa]       │
       │  [JS actualiza campos hidden/readonly] │
       │                                        │
       │  [Usuario clic en "Registrar Ruta"]    │
       │  JS intercepta evento submit()         │
       │                                        │
       │  POST /php/registrar_ruta_dinamica.php │
       │  Content-Type: application/x-www-form-urlencoded
       │  ─────────────────────────────────────►│
       │  Body:                                 │
       │    nombre_ruta = "Ruta Villahermosa"   │
       │    origen = "17.9895,-92.9256"         │
       │    destino = "18.0021,-92.8754"        │
       │    paradas = "17.99,-92.91|18.00,-92.89"
       │                                        │
       │                              ┌─────────┤
       │                              │ PHP:    │
       │                              │ explode │
       │                              │ origen/ │
       │                              │ destino │
       │                              │         │
       │                              │ INSERT  │
       │                              │ INTO    │
       │                              │ rutas   │
       │                              │         │
       │                              │lastInsertId()
       │                              │         │
       │                              │ foreach │
       │                              │ paradas │
       │                              │ INSERT  │
       │                              │ INTO    │
       │                              │ paradas │
       │                              └─────────┤
       │                                        │
       │  ◄──── 302 Location: ver_rutas.php ────│
       │             ?mensaje=exito             │
       │                                        │
       │  GET /ver_rutas.php?mensaje=exito      │
       │ ─────────────────────────────────────► │
       │  ◄───────────── 200 OK ─────────────── │
       │     (Listado con mensaje de éxito)     │
```

**Payload del POST — Detalle de campos:**

| Campo | Formato | Ejemplo |
|-------|---------|---------|
| `nombre_ruta` | Texto libre | `"Ruta CEDIS-Regional"` |
| `origen` | `"lat,lon"` | `"17.9895,-92.9256"` |
| `destino` | `"lat,lon"` | `"18.0021,-92.8754"` |
| `paradas` | `"lat,lon|lat,lon|..."` | `"17.99,-92.91|18.00,-92.87"` |

---

### 3. Visualización de Ruta Específica (GET con parámetro)
Accedida desde `ver_rutas.php` al hacer clic en "Ver Mapa".

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  GET /ver_mapa.php?id=5                │
       │ ─────────────────────────────────────► │
       │                                        │
       │                              ┌─────────┤
       │                              │ PHP:    │
       │                              │ $_GET   │
       │                              │ ['id']  │
       │                              │         │
       │                              │ SELECT  │
       │                              │ FROM    │
       │                              │ rutas   │
       │                              │ WHERE   │
       │                              │ id=5    │
       │                              │         │
       │                              │ SELECT  │
       │                              │ FROM    │
       │                              │ paradas │
       │                              │ WHERE   │
       │                              │ id_ruta=5
       │                              └─────────┤
       │                                        │
       │  ◄───────────── 200 OK ─────────────── │
       │  (HTML con Leaflet + coords inyectadas)│
       │                                        │
       │  [Leaflet renderiza marcadores:        │
       │   Origen, Destino y Paradas]           │
```

---

### 4. Eliminación de Ruta (GET con parámetro)
Flujo de borrado desde el listado en `ver_rutas.php`.

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [Usuario clic en "Eliminar"]          │
       │  JS: confirm("¿Está seguro?")          │
       │      → true                            │
       │  window.location.href =                │
       │    "php/eliminar_ruta.php?id=5"        │
       │                                        │
       │  GET /php/eliminar_ruta.php?id=5       │
       │ ─────────────────────────────────────► │
       │                                        │
       │                              ┌─────────┤
       │                              │ PHP:    │
       │                              │ $_GET   │
       │                              │ ['id']  │
       │                              │         │
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ paradas │
       │                              │ WHERE   │
       │                              │ id_ruta=5
       │                              │         │
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ rutas   │
       │                              │ WHERE   │
       │                              │ id=5    │
       │                              └─────────┤
       │                                        │
       │  ◄──── 302 Location: ver_rutas.php ────│
       │             ?mensaje=eliminado         │
```

---

### 5. Edición de Ruta (GET + POST)
Flujo de dos pasos: carga del formulario con datos existentes, luego actualización.

```
PASO 1 — Carga del formulario de edición
─────────────────────────────────────────
Cliente                                Servidor
   │                                      │
   │  GET /editar_ruta_dinamica.php?id=5  │
   │ ────────────────────────────────────►│
   │                              SELECT  │
   │                              ruta +  │
   │                              paradas │
   │                              WHERE id=5
   │  ◄──────── 200 OK (HTML prefilled) ──│


PASO 2 — Envío de actualización
────────────────────────────────
Cliente                                Servidor
   │                                      │
   │  POST /php/actualizar_ruta_dinamica.php
   │ ────────────────────────────────────►│
   │  Body:                               │
   │    id_ruta    = 5                    │
   │    nombre_ruta = "Ruta Actualizada"  │
   │    origen      = "17.99,-92.92"      │
   │    destino     = "18.01,-92.87"      │
   │    paradas     = "17.995,-92.90"     │
   │                              DELETE  │
   │                              paradas │
   │                              WHERE   │
   │                              id_ruta=5
   │                              UPDATE  │
   │                              rutas   │
   │                              INSERT  │
   │                              paradas │
   │  ◄──── 302 Location: ver_rutas.php ──│
```

---

## Estructura de Datos

### Tabla: `rutas`
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_ruta` | INT AUTO_INCREMENT | PK | Identificador único de la ruta. |
| `nombre_ruta` | VARCHAR(150) | — | Nombre descriptivo del trayecto. |
| `lat_origen` | DECIMAL(10,7) | — | Latitud del punto de inicio. |
| `lon_origen` | DECIMAL(10,7) | — | Longitud del punto de inicio. |
| `lat_destino` | DECIMAL(10,7) | — | Latitud del punto de llegada. |
| `lon_destino` | DECIMAL(10,7) | — | Longitud del punto de llegada. |
| `distancia_total` | DECIMAL(10,2) | — | Distancia calculada en kilómetros. |

### Tabla: `paradas`
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_parada` | INT AUTO_INCREMENT | PK | Identificador único de la parada. |
| `id_ruta` | INT | FK → `rutas` | Ruta a la que pertenece. |
| `orden` | TINYINT | — | Posición secuencial en el trayecto. |
| `latitud` | DECIMAL(10,7) | — | Latitud de la parada. |
| `longitud` | DECIMAL(10,7) | — | Longitud de la parada. |

### Diagrama Entidad-Relación
```
  ┌─────────────┐          ┌─────────────┐
  │    rutas    │          │   paradas   │
  ├─────────────┤  1 : N   ├─────────────┤
  │ id_ruta  PK │◄─────────│ id_parada PK│
  │ nombre_ruta │          │ id_ruta  FK │
  │ lat_origen  │          │ orden       │
  │ lon_origen  │          │ latitud     │
  │ lat_destino │          │ longitud    │
  │ lon_destino │          └─────────────┘
  │ distancia   │
  └─────────────┘
```

---

## Flujo de Datos Completo (Registro)
```
  ┌──────────┐    clic en mapa    ┌────────────┐
  │ Usuario  │──────────────────► │  Leaflet   │
  └──────────┘                   └─────┬──────┘
                                        │ evento 'click'
                                        ▼
                                  ┌───────────┐
                                  │ JS Handler│
                                  │ (inline)  │
                                  └─────┬─────┘
                                        │ actualiza
                                        ▼
                               ┌──────────────────┐
                               │ Campos del Form  │
                               │ #origen (hidden) │
                               │ #destino (hidden)│
                               │ #paradas (hidden)│
                               └────────┬─────────┘
                                        │ submit POST
                                        ▼
                        ┌───────────────────────────┐
                        │ php/registrar_ruta_din.php │
                        ├───────────────────────────┤
                        │ 1. explode(',', $origen)   │
                        │ 2. INSERT INTO rutas       │
                        │ 3. lastInsertId() → $id    │
                        │ 4. explode('|', $paradas)  │
                        │ 5. foreach → INSERT parada │
                        └─────────────┬─────────────┘
                                      │ redirect
                                      ▼
                               ┌─────────────┐
                               │ ver_rutas   │
                               │   .php      │
                               └─────────────┘
```

---

## Códigos de Respuesta y Parámetros de Mensajería
| Parámetro GET (`?mensaje=`) | Significado | Pantalla destino |
|-----------------------------|-------------|-----------------|
| `exito` | Ruta registrada correctamente. | `ver_rutas.php` |
| `actualizado` | Ruta modificada con éxito. | `ver_rutas.php` |
| `eliminado` | Ruta y paradas eliminadas. | `ver_rutas.php` |
| `error` | Fallo en la operación de BD. | Formulario origen |

---

## Notas de Mantenimiento
- **Eliminación**: La ruta debe eliminar primero sus `paradas` antes de borrar el registro en `rutas` para respetar la restricción de integridad referencial. El orden correcto es: `DELETE paradas WHERE id_ruta=X` → `DELETE rutas WHERE id=X`.
- **Coordenadas como string**: Se almacenan como strings decimales para facilitar el `explode()` en PHP. Considerar migrar a `DECIMAL(10,7)` para operaciones geoespaciales futuras.
- **Sin autenticación de tiles**: Las peticiones a OpenStreetMap son anónimas. Si se supera el rate limit, los tiles dejarán de cargar. Evaluar Mapbox o self-hosted para producción.
- **Edición**: Al actualizar una ruta, las paradas existentes se eliminan y se reinsertan. No se realiza un `diff` de paradas.
