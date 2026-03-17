# Implementaciones AJAX en SIGERUTT

## ¿Qué es AJAX?

**AJAX** (Asynchronous JavaScript And XML) es una técnica de desarrollo web que permite que las páginas web se comuniquen con el servidor **sin necesidad de recargar la página completa**. En este proyecto utilizamos la **Fetch API** de JavaScript moderno como mecanismo para las peticiones asíncronas.

### Beneficios implementados:
- ✅ **Sin recargas de página**: Los formularios se envían y procesan sin perder el estado actual
- ✅ **Mensajes dinámicos**: Los mensajes de éxito/error aparecen instantáneamente en la misma página
- ✅ **Eliminación dinámica**: Las filas de las tablas desaparecen con animación al eliminar un registro
- ✅ **Compatibilidad**: Se mantiene el funcionamiento tradicional como respaldo

---

## Arquitectura General

### Flujo AJAX implementado

```
┌──────────────────────┐     fetch() + FormData       ┌──────────────────────┐
│                      │  ─────────────────────────►  │                      │
│   NAVEGADOR (JS)     │   Header: X-Requested-With   │   SERVIDOR (PHP)     │
│   js/scripts.js      │   XMLHttpRequest             │   php/*.php          │
│                      │  ◄─────────────────────────  │                      │
│   Muestra mensaje    │     JSON: {status, mensaje}  │   Procesa y responde │
│   dinámico en el DOM │                              │   con JSON           │
└──────────────────────┘                              └──────────────────────┘
```

### Detección de petición AJAX (PHP)

Cada handler PHP detecta si la petición viene de AJAX verificando el header `X-Requested-With`:

```php
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
```

Si es AJAX, responde con JSON. Si no, mantiene el comportamiento tradicional con `header('Location: ...')`.

### Función de respuesta (PHP)

```php
function responder($esAjax, $redirectUrl, $status, $mensaje) {
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'mensaje' => $mensaje]);
        exit;
    }
    header('Location: ' . $redirectUrl);
    exit;
}
```

### Envío de formulario con fetch (JavaScript)

```javascript
function enviarFormularioAjax(form, onExito) {
    let formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        let tipo = (data.status === 'ok') ? 'exito' : 'error';
        mostrarMensaje(tipo, data.mensaje, form);

        if (data.status === 'ok') {
            if (onExito) onExito(data);
            else form.reset();
        }
    })
    .catch(error => {
        mostrarMensaje('error', 'Error de conexión.', form);
    });
}
```

---

## Implementaciones por Módulo

### 1. Login (`login.php` → `php/login.php`)

**Antes (tradicional):**
```
Usuario envía formulario → PHP procesa → header('Location: login.php?mensaje=error') → Página recarga
```

**Después (AJAX):**
```
Usuario envía formulario → JavaScript intercepta con e.preventDefault()
→ fetch() envía datos al servidor → PHP responde JSON
→ JavaScript muestra mensaje SIN recargar la página
→ Si login exitoso: redirige a index.php vía JavaScript
```

**Código JavaScript relevante:**
```javascript
document.getElementById('formLogin').addEventListener('submit', function (e) {
    e.preventDefault();  // Evita el envío tradicional

    enviarFormularioAjax(this, function (data) {
        if (data.redirect) {
            window.location.href = data.redirect;  // Redirige solo si es exitoso
        }
    });
});
```

**Respuesta JSON del servidor:**
```json
// Error:
{"status": "error", "mensaje": "Usuario o contraseÃ±a incorrectos."}

// Éxito:
{"status": "ok", "mensaje": "Inicio de sesión exitoso.", "redirect": "/SIGERUTT/index.php"}
```

---

### 2. Registro de Usuarios (`registrar_usuario.php` → `php/registrar_usuario.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formUsuario`
- Validación en cliente: campos vacíos, longitud de contraseÃ±a (≥ 6 caracteres)
- Envía datos con `fetch()` usando `FormData`
- El servidor valida: campos vacíos, email válido, nombre no duplicado, correo no duplicado
- Responde JSON con el resultado
- JavaScript muestra mensaje de éxito/error dinámicamente y limpia el formulario si fue exitoso

**Respuestas JSON posibles:**
```json
{"status": "ok", "mensaje": "Usuario registrado correctamente."}
{"status": "error", "mensaje": "El nombre completo ya está registrado."}
{"status": "error", "mensaje": "El correo ya está registrado."}
{"status": "error", "mensaje": "La contraseÃ±a debe tener al menos 6 caracteres."}
```

---

### 3. Registro de Vehículos (`registrar_vehiculo.php` → `php/registrar_vehiculo.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formVehiculo`
- Validación en cliente: campos obligatorios, placa de 9 caracteres
- La capacidad se asigna automáticamente según el tipo (lógica existente de `change` en el select)
- Envía datos con `fetch()`
- El servidor valida: placa no duplicada, tipo válido
- Responde JSON

**Respuestas JSON posibles:**
```json
{"status": "ok", "mensaje": "Vehículo registrado correctamente."}
{"status": "error", "mensaje": "La placa ya está registrada."}
{"status": "error", "mensaje": "La placa debe tener exactamente 9 caracteres."}
```

---

### 4. Registro de Operadores (`registrar_operador.php` → `php/registrar_operador.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formOperador`
- Validación en cliente: nombre, licencia, teléfono obligatorios
- Envía datos con `fetch()`
- El servidor valida: licencia no duplicada
- Responde JSON

**Respuestas JSON posibles:**
```json
{"status": "ok", "mensaje": "Operador registrado correctamente."}
{"status": "error", "mensaje": "La licencia ya está registrada."}
```

---

### 5. Registro de Rutas (`registrar_ruta.php` → `php/registrar_ruta.php`)

**Implementación AJAX (doble):**

Este módulo tiene **dos** usos de AJAX:

#### a) Búsqueda de código postal (OpenStreetMap) — Ya existía
```javascript
fetch(`https://nominatim.openstreetmap.org/search?postalcode=${codigoPostal}&country=Mexico&format=json`)
    .then(response => response.json())
    .then(data => { /* centra el mapa en la ubicación */ });
```

#### b) Envío del formulario de registro de ruta — Nuevo
```javascript
formRutaMapa.addEventListener('submit', function (e) {
    e.preventDefault();
    enviarFormularioAjax(this, function (data) {
        mostrarMensaje('exito', data.mensaje, formRutaMapa);
        formRutaMapa.reset();
        // Limpia los campos ocultos del mapa
    });
});
```

**Respuestas JSON:**
```json
{"status": "ok", "mensaje": "Ruta registrada correctamente."}
{"status": "error", "mensaje": "Faltan datos obligatorios."}
```

---

### 6. Registro de Ruta Dinámica (`registrar_ruta_dinamica.php` → `php/registrar_ruta_dinamica.php`)

**Implementación AJAX:**
- Intercepta el formulario que apunta a `php/registrar_ruta_dinamica.php`
- Valida que se hayan marcado origen y destino en el mapa
- Envía los datos con `fetch()` y muestra resultado dinámicamente

---

### 7. Asignar Rutas (`asignaciones.php` → `php/asignaciones.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formAsignacion`
- Validación en cliente: selección de ruta, vehículo, operador y fecha obligatorios
- Envía datos con `fetch()`
- Responde JSON

**Respuestas JSON:**
```json
{"status": "ok", "mensaje": "Ruta asignada correctamente."}
{"status": "error", "mensaje": "Todos los campos son obligatorios."}
```

---

### 8. Editar Usuario (`php/editar_usuario.php` → `php/actualizar_usuario.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formUsuario` en la página de edición
- Envía datos con `fetch()` al handler `actualizar_usuario.php`
- El servidor actualiza el registro y responde JSON
- JavaScript muestra mensaje de éxito sin recargar

**Respuesta JSON:**
```json
{"status": "ok", "mensaje": "Usuario actualizado correctamente."}
```

---

### 9. Editar Vehículo (`php/editar_vehiculo.php` → `php/actualizar_vehiculo.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formVehiculo` en la página de edición
- Incluye la lógica de placa en mayúsculas y capacidad automática
- Envía con `fetch()`, responde JSON

---

### 10. Editar Operador (`php/editar_operador.php` → `php/actualizar_operador.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formOperador` en la página de edición
- Envía con `fetch()`, responde JSON

---

### 11. Editar Asignación (`php/editar_asignacion.php` → `php/actualizar_asignacion.php`)

**Implementación AJAX:**
- Intercepta el formulario `#formAsignacion` en la página de edición
- Envía con `fetch()`, responde JSON

---

### 12. Editar Ruta Dinámica (`editar_ruta_dinamica.php` → `php/actualizar_ruta_dinamica.php`)

**Implementación AJAX:**
- Intercepta el formulario que apunta a `php/actualizar_ruta_dinamica.php`
- Valida origen/destino, envía con `fetch()`, muestra resultado dinámicamente

---

### 13. Eliminar Registros (Usuarios, Vehículos, Operadores, Rutas, Asignaciones)

**Antes (tradicional):**
```
Clic en "Eliminar" → confirm() → window.location.href → PHP elimina → header('Location: ...')
→ Página recarga completa
```

**Después (AJAX):**
```
Clic en "Eliminar" → confirm() → fetch() envía petición
→ PHP elimina y responde JSON
→ JavaScript elimina la fila <tr> con animación → Muestra mensaje de éxito
```

**Código JavaScript:**
```javascript
function eliminarRegistroAjax(url, fila) {
    fetch(url, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            // Animación de desaparición
            fila.style.transition = 'opacity 0.4s, transform 0.4s';
            fila.style.opacity = '0';
            fila.style.transform = 'translateX(-20px)';
            setTimeout(() => fila.remove(), 400);
            mostrarMensaje('exito', data.mensaje, null);
        }
    });
}
```

**Archivos PHP afectados:**
| Archivo | Tabla | Respuesta JSON |
|---------|-------|----------------|
| `php/eliminar_usuario.php` | `usuarios` | `Usuario eliminado correctamente.` |
| `php/eliminar_vehiculo.php` | `vehiculos` | `Vehículo eliminado correctamente.` |
| `php/eliminar_operador.php` | `operadores` | `Operador eliminado correctamente.` |
| `php/eliminar_ruta.php` | `rutas` | `Ruta eliminada correctamente.` |
| `php/eliminar_asignacion.php` | `asignaciones` | `Asignación eliminada correctamente.` |

---

## Función Utilitaria: `mostrarMensaje()`

Esta función reutilizable se encarga de insertar mensajes dinámicos en el DOM:

```javascript
function mostrarMensaje(tipo, texto, referencia) {
    // Elimina mensajes previos
    document.querySelectorAll('.mensaje-ajax-dinamico').forEach(m => m.remove());

    let div = document.createElement('div');
    div.className = 'mensaje ' + tipo + ' mensaje-ajax-dinamico';
    div.textContent = texto;

    // Inserta el mensaje antes del formulario o después del <h2>
    if (referencia) {
        referencia.parentNode.insertBefore(div, referencia);
    } else {
        let h2 = document.querySelector('h2');
        if (h2) h2.parentNode.insertBefore(div, h2.nextSibling);
    }

    // Auto-ocultar después de 5 segundos con animación
    setTimeout(() => {
        div.style.transition = 'opacity 0.5s';
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 500);
    }, 5000);
}
```

---

## Resumen de Archivos Modificados

### Backend PHP (17 archivos)
| # | Archivo | Descripción |
|---|---------|-------------|
| 1 | `php/login.php` | Autenticación con respuesta JSON |
| 2 | `php/registrar_usuario.php` | Registro de usuario con JSON |
| 3 | `php/registrar_vehiculo.php` | Registro de vehículo con JSON |
| 4 | `php/registrar_operador.php` | Registro de operador con JSON |
| 5 | `php/registrar_ruta.php` | Registro de ruta con JSON |
| 6 | `php/registrar_ruta_dinamica.php` | Registro de ruta dinámica con JSON |
| 7 | `php/asignaciones.php` | Crear asignación con JSON |
| 8 | `php/actualizar_usuario.php` | Actualizar usuario con JSON |
| 9 | `php/actualizar_vehiculo.php` | Actualizar vehículo con JSON |
| 10 | `php/actualizar_operador.php` | Actualizar operador con JSON |
| 11 | `php/actualizar_asignacion.php` | Actualizar asignación con JSON |
| 12 | `php/actualizar_ruta_dinamica.php` | Actualizar ruta dinámica con JSON |
| 13 | `php/eliminar_usuario.php` | Eliminar usuario con JSON |
| 14 | `php/eliminar_vehiculo.php` | Eliminar vehículo con JSON |
| 15 | `php/eliminar_operador.php` | Eliminar operador con JSON |
| 16 | `php/eliminar_ruta.php` | Eliminar ruta con JSON |
| 17 | `php/eliminar_asignacion.php` | Eliminar asignación con JSON |

### Frontend (5 archivos)
| # | Archivo | Cambio |
|---|---------|--------|
| 1 | `js/scripts.js` | Reescrito con `fetch()` API |
| 2 | `login.php` | Agregado contenedor AJAX + referencia a `scripts.js` |
| 3 | `registrar_ruta.php` | Agregada referencia a `scripts.js` |
| 4 | `registrar_ruta_dinamica.php` | Agregada referencia a `scripts.js` |
| 5 | `editar_ruta_dinamica.php` | Agregada referencia a `scripts.js` |

---

## Validaciones Asíncronas con AJAX

Una de las ventajas principales de usar AJAX es poder realizar **validaciones asíncronas**: el servidor verifica los datos enviados (por ejemplo, si un correo ya existe en la base de datos) **sin recargar la página**, y devuelve el resultado instantáneamente al usuario.

### ¿Qué es una validación asíncrona?

Es una validación que **requiere comunicación con el servidor** para verificar datos contra la base de datos, pero que se ejecuta **en segundo plano** sin interrumpir la experiencia del usuario.

```
┌────────────────┐                               ┌────────────────┐
│   NAVEGADOR    │  1. Validación local (JS)     │                │
│                │  ───────────────────────►     │                │
│  El usuario    │  2. Si pasa, envía fetch()    │   SERVIDOR     │
│  llena el      │  ───────────────────────►     │   (PHP + BD)   │
│  formulario    │                               │                │
│                │  3. PHP consulta la BD        │  ¿Ya existe    │
│                │  ◄───────────────────────     │  ese dato?     │
│  4. JS muestra │     JSON con resultado        │                │
│  el resultado  │                               │                │
└────────────────┘                               └────────────────┘
```

### Validaciones asíncronas en cada módulo

#### 🔐 Login — Validación de credenciales

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Campos vacíos | `js/scripts.js` |
| 2 | **Asíncrona (AJAX)** | ¿El correo existe en la BD? | `php/login.php` |
| 3 | **Asíncrona (AJAX)** | ¿La contraseña coincide con el hash? | `php/login.php` |

```javascript
// Paso 1: Validación local en JavaScript (síncrona)
if (correo === '' || contraseña === '') {
    mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
    return; // Detiene aquí, NO se hace la petición al servidor
}

// Paso 2 y 3: Si pasa la validación local, se envía al servidor (asíncrona)
enviarFormularioAjax(this, function (data) {
    // El servidor ya verificó correo + contraseña contra la BD
    if (data.redirect) {
        window.location.href = data.redirect;
    }
});
```

```php
// En php/login.php — El servidor verifica contra la BD de forma asíncrona
$stmt->execute([$correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
    // Credenciales válidas
    responder($esAjax, ..., 'ok', 'Inicio de sesión exitoso.');
} else {
    // Credenciales inválidas
    responder($esAjax, ..., 'error', 'Usuario o contraseña incorrectos.');
}
```

---

#### 👤 Registro de Usuarios — Validación de duplicados

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Campos vacíos | `js/scripts.js` |
| 2 | Síncrona (JS) | Contraseña ≥ 6 caracteres | `js/scripts.js` |
| 3 | **Asíncrona (AJAX)** | ¿Email válido? | `php/registrar_usuario.php` |
| 4 | **Asíncrona (AJAX)** | ¿Nombre ya registrado en BD? | `php/registrar_usuario.php` |
| 5 | **Asíncrona (AJAX)** | ¿Correo ya registrado en BD? | `php/registrar_usuario.php` |

```php
// Validación asíncrona: verificar nombre duplicado contra la BD
$sql_nombre = "SELECT * FROM usuarios WHERE LOWER(nombre) = LOWER(?)";
$stmt_nombre = $conexion->prepare($sql_nombre);
$stmt_nombre->execute([$nombre]);

if ($stmt_nombre->fetch()) {
    // El nombre YA existe → responde JSON con error (sin recargar)
    responder($esAjax, ..., 'error', 'El nombre completo ya está registrado.');
}

// Validación asíncrona: verificar correo duplicado contra la BD
$sql_correo = "SELECT * FROM usuarios WHERE LOWER(correo) = LOWER(?)";
$stmt_correo = $conexion->prepare($sql_correo);
$stmt_correo->execute([$correo]);

if ($stmt_correo->fetch()) {
    responder($esAjax, ..., 'error', 'El correo ya está registrado.');
}
```

---

#### 🚗 Registro de Vehículos — Validación de placa duplicada

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Campos vacíos | `js/scripts.js` |
| 2 | Síncrona (JS) | Placa = 9 caracteres exactos | `js/scripts.js` |
| 3 | **Asíncrona (AJAX)** | ¿Placa ya registrada en BD? | `php/registrar_vehiculo.php` |
| 4 | **Asíncrona (AJAX)** | ¿Tipo de vehículo válido? | `php/registrar_vehiculo.php` |

```php
// Validación asíncrona: placa duplicada
$sql_check = "SELECT * FROM vehiculos WHERE LOWER(placa) = LOWER(?)";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->execute([$placa]);

if ($stmt_check->fetch()) {
    responder($esAjax, ..., 'error', 'La placa ya está registrada.');
}
```

---

#### 👷 Registro de Operadores — Validación de licencia duplicada

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Campos vacíos | `js/scripts.js` |
| 2 | **Asíncrona (AJAX)** | ¿Licencia ya registrada en BD? | `php/registrar_operador.php` |

```php
// Validación asíncrona: licencia duplicada
$sql_check = "SELECT * FROM operadores WHERE LOWER(licencia) = LOWER(?)";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->execute([$licencia]);

if ($stmt_check->fetch()) {
    responder($esAjax, ..., 'error', 'La licencia ya está registrada.');
}
```

---

#### 🗺️ Registro de Rutas — Validación de coordenadas + Geocodificación

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Nombre de ruta, origen, destino no vacíos | `js/scripts.js` |
| 2 | **Asíncrona (AJAX externo)** | Búsqueda de código postal en OpenStreetMap | `registrar_ruta.php` (fetch a API externa) |
| 3 | **Asíncrona (AJAX)** | Inserción de ruta en la BD | `php/registrar_ruta.php` |

```javascript
// Validación asíncrona externa: consulta a la API de OpenStreetMap
fetch(`https://nominatim.openstreetmap.org/search?postalcode=${codigoPostal}&country=Mexico&format=json`)
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            // Código postal encontrado → centra el mapa
            agregarPunto(latlng);
            map.setView(latlng, 15);
        } else {
            // Código postal NO encontrado → muestra alerta
            alert('Código postal no encontrado. Intenta con otro.');
        }
    });
```

---

#### 📋 Asignar Rutas — Validación de campos

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Ruta, vehículo, operador y fecha seleccionados | `js/scripts.js` |
| 2 | **Asíncrona (AJAX)** | Validación de campos + inserción en BD | `php/asignaciones.php` |

---

#### 🗑️ Eliminaciones — Validación de existencia

| Paso | Tipo | Validación | ¿Dónde? |
|------|------|-----------|---------|
| 1 | Síncrona (JS) | Confirmación del usuario (`confirm()`) | `js/scripts.js` |
| 2 | **Asíncrona (AJAX)** | ¿El ID existe? + eliminación en BD | `php/eliminar_*.php` |

---

### Resumen de validaciones por tipo

| Tipo de Validación | Tecnología | Ejemplo | Requiere servidor |
|---|---|---|---|
| **Síncrona (cliente)** | JavaScript puro | Campos vacíos, longitud de contraseña, formato de placa | ❌ No |
| **Asíncrona (servidor)** | fetch() + PHP + MySQL | Correo/nombre/placa/licencia duplicados | ✅ Sí |
| **Asíncrona (API externa)** | fetch() + API REST | Búsqueda de código postal en OpenStreetMap | ✅ Sí (API externa) |

> **Nota para la exposición:** La diferencia clave es que las validaciones **síncronas** se ejecutan instantáneamente en el navegador sin contactar al servidor, mientras que las **asíncronas** necesitan enviar una petición al servidor (o API externa) y esperar su respuesta — todo esto ocurre **sin recargar la página** gracias a AJAX.

---

## Tecnologías Utilizadas

| Tecnología | Uso en el proyecto |
|---|---|
| **Fetch API** | Envío de peticiones HTTP asíncronas desde el navegador |
| **FormData** | Serialización de datos de formularios para enviar vía fetch |
| **JSON** | Formato de respuesta del servidor PHP |
| **X-Requested-With** | Header HTTP para identificar peticiones AJAX en el servidor |
| **DOM API** | Manipulación dinámica del HTML para mostrar mensajes |
