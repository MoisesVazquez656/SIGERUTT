# Vehículos — Documentación para Desarrolladores

## Descripción General
Gestiona la flota de transporte terrestre de SIGERUTT. Asigna capacidades de carga automáticamente según el tipo de unidad. El campo `estado` (`activo`/`inactivo`) determina la visibilidad del vehículo en el módulo de Asignaciones.

---

## Archivos del Módulo
| Archivo | Propósito |
|---------|-----------|
| `registrar_vehiculo.php` | Formulario de alta para nuevas unidades. |
| `ver_vehiculos.php` | Listado y control de estado de la flota. |
| `php/registrar_vehiculo.php` | Inserción, lookup de capacidades y validaciones. |
| `php/editar_vehiculo.php` | Formulario de edición con datos precargados. |
| `php/actualizar_vehiculo.php` | Procesamiento de cambios en la unidad. |
| `php/eliminar_vehiculo.php` | Remoción de vehículos del catálogo. |

---

## Lógica de Capacidades (Lookup Automático)
```php
$capacidades = [
    'Camión de carga ligera'  => '1,500 kg',
    'Camión de carga pesada'  => '10 toneladas',
    'Tráiler'                 => '20 toneladas',
    'Torton'                  => '15 toneladas',
    'Rabón'                   => '8 toneladas',
    'Caja seca'               => '10 toneladas',
    'Refrigerado'             => '12 toneladas',
    'Plataforma'              => '25 toneladas',
    'Camioneta tipo van'      => '800 kg',
    'Motocicleta de reparto'  => '150 kg',
];
$capacidad = $capacidades[$tipo] ?? 'Desconocida';
```

---

## Solicitudes HTTP del Módulo

### 1. Registro de Nuevo Vehículo (POST)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  GET /registrar_vehiculo.php           │
       │ ─────────────────────────────────────► │
       │  ◄──────────── 200 OK ──────────────── │
       │                                        │
       │  [Usuario llena placa y elige tipo]    │
       │  [JS: toUpperCase() en campo placa]    │
       │  [JS: valida placa.length === 9]       │
       │                                        │
       │  POST /php/registrar_vehiculo.php      │
       │  Content-Type: application/x-www-form-urlencoded
       │ ─────────────────────────────────────► │
       │  Body:                                 │
       │    placa  = "AB-123-CD"                │
       │    tipo   = "Tráiler"                  │
       │    estado = "activo"                   │
       │                                        │
       │                              ┌─────────┤
       │                              │ empty() │
       │                              │ strlen  │
       │                              │ placa=9 │
       │                              │         │
       │                              │ SELECT  │
       │                              │ placa   │
       │                              │ dup?    │
       │                              │ LOWER() │
       │                              │         │
       │               Si duplicado: ─┤         │
       │  ◄── 302 ?mensaje=placa_rep  │         │
       │                              │         │
       │               Si OK:         │         │
       │                              │ Lookup  │
       │                              │ capac.  │
       │                              │ INSERT  │
       │                              │ vehicu- │
       │                              │ los     │
       │                              └─────────┤
       │  ◄─── 302 ver_vehiculos.php?exito ─────│
```

**Payload POST:**

| Campo | Validación Cliente | Validación Servidor |
|-------|--------------------|---------------------|
| `placa` | JS: length===9, toUpperCase | strlen===9, LOWER() dup |
| `tipo` | required HTML5 | !empty() + lookup |
| `estado` | required HTML5 | !empty() |
| `capacidad` | readonly (no se envía) | Generada por servidor |

---

### 2. Validación de Placa en Cliente (JS)

```
  form.addEventListener('submit', fn)
                │
                ▼
    placa = input.value.toUpperCase()
    input.value = placa
                │
    ¿placa.length === 9?
   /                    \
 NO                     SÍ
  │                      │
  ▼                      ▼
e.preventDefault()   POST al servidor
alert(error)
```

---

### 3. Edición de Vehículo (GET + POST)

```
PASO 1 — Carga del formulario
──────────────────────────────
  GET /php/editar_vehiculo.php?id=7
         │
         ▼  SELECT * FROM vehiculos WHERE id=7
         ▼
  200 OK (form con placa/tipo/estado precargados)


PASO 2 — Actualización
───────────────────────
  POST /php/actualizar_vehiculo.php
  Body: id_vehiculo=7, placa, tipo, estado
         │
         ▼  Valida dup placa (excl. self)
         ▼  Lookup nueva capacidad por tipo
         ▼  UPDATE vehiculos SET ... WHERE id=7
         ▼
  302 → ver_vehiculos.php?mensaje=actualizado
```

---

### 4. Eliminación de Vehículo (GET)

```
Cliente (Navegador)                     Servidor Apache / PHP
       │                                        │
       │  [clic "Eliminar"] → confirm()         │
       │  window.location.href =                │
       │    "php/eliminar_vehiculo.php?id=7"    │
       │                                        │
       │  GET /php/eliminar_vehiculo.php?id=7   │
       │ ─────────────────────────────────────► │
       │                              ┌─────────┤
       │                              │ DELETE  │
       │                              │ FROM    │
       │                              │ vehicu- │
       │                              │ los     │
       │                              │ WHERE   │
       │                              │ id=7    │
       │                              └─────────┤
       │  ◄── 302 ver_vehiculos.php?eliminado ──│
```

---

## Estructura de Datos

### Tabla: `vehiculos`
| Campo | Tipo | PK/FK | Descripción |
|-------|------|-------|-------------|
| `id_vehiculo` | INT AUTO_INCREMENT | PK | Identificador único. |
| `placa` | VARCHAR(9) | UNIQUE | Placa oficial (máx. 9 chars). |
| `tipo` | VARCHAR(100) | — | Categoría del vehículo. |
| `capacidad` | VARCHAR(50) | — | Valor generado por backend. |
| `estado` | ENUM | — | `activo`, `inactivo`. |

### Relación con Asignaciones
```
  ┌─────────────────┐          ┌──────────────────┐
  │    vehiculos    │  1 : N   │   asignaciones   │
  ├─────────────────┤◄─────────├──────────────────┤
  │ id_vehiculo  PK │          │ id_vehiculo FK   │
  │ placa UNIQUE    │          │ id_ruta FK       │
  │ tipo            │          │ id_operador FK   │
  │ capacidad       │          │ fecha_asignacion │
  │ estado ENUM     │          └──────────────────┘
  └─────────────────┘
  estado='activo'   → disponible en Asignaciones
  estado='inactivo' → oculto en Asignaciones
```

---

## Flujo Completo de Registro
```
 ┌──────────┐  └─placa/tipo/estado─┐  ┌──────────────────────┐
 │ Usuario  │──────────────────────►│ registrar_vehiculo.php │
 └──────────┘                       └──────────┬─────────────┘
                                               │ JS submit
                                    ┌──────────▼─────────────┐
                                    │ Normaliza MAYÚSCULAS   │
                                    │ Verifica length === 9  │
                                    └──────────┬─────────────┘
                                               │ POST
                                    ┌──────────▼─────────────┐
                                    │ php/registrar_vehiculo │
                                    ├────────────────────────┤
                                    │ 1. empty() check       │
                                    │ 2. strlen(placa) === 9 │
                                    │ 3. dup LOWER(placa)    │
                                    │ 4. $capacidades[$tipo] │
                                    │ 5. INSERT INTO vehiculos
                                    └──────────┬─────────────┘
                                               │
                        ┌──────────────────────┴──────────────┐
                        │ ?exito  ?placa_repetida  ?campos_vacios
                        └─────────────────────────────────────┘
                                               │
                                    ┌──────────▼─────────────┐
                                    │   ver_vehiculos.php    │
                                    └────────────────────────┘
```

---

## Códigos de Mensajería
| `?mensaje=` | Significado | Destino |
|-------------|-------------|---------|
| `exito` | Registro exitoso | `ver_vehiculos.php` |
| `actualizado` | Datos actualizados | `ver_vehiculos.php` |
| `eliminado` | Vehículo removido | `ver_vehiculos.php` |
| `placa_repetida` | Placa ya registrada | Formulario origen |
| `campos_vacios` | Campos incompletos | Formulario origen |

---

## Notas de Mantenimiento
- **Nuevo tipo de vehículo**: Actualizar el array `$capacidades` en ambos archivos: `php/registrar_vehiculo.php` y `php/actualizar_vehiculo.php`.
- **Eliminación segura**: Verificar `SELECT COUNT(*) FROM asignaciones WHERE id_vehiculo = X` antes de ejecutar `DELETE`. Preferir cambiar `estado` a `inactivo`.
- **Formato de placa**: Solo se valida longitud (9 chars). Considerar regex `/^[A-Z0-9\-]{9}$/` para mayor control.
