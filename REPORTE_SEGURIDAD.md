# Reporte de Implementación de Seguridad - SIGERUTT

Este documento detalla las mejoras de seguridad realizadas en el sistema SIGERUTT para fortalecer la protección de rutas, prevenir ataques de Inyección SQL y mitigar riesgos de CSRF.

## 1. Protección de Rutas y Control de Acceso (RBAC)

Se ha implementado un sistema de **Control de Acceso Basado en Roles (RBAC)** para asegurar que las funciones administrativas no sean accesibles por usuarios con privilegios insuficientes.

### Cambios realizados:
- **Estandarización de Verificación:** Se reemplazó la verificación genérica `require_login()` por validaciones específicas como `require_admin()` o `require_role('admin', 'supervisor')` en los scripts críticos del backend.
- **Módulos Protegidos:**
    - Registro y edición de **Rutas**.
    - Registro y edición de **Vehículos**.
    - Registro y edición de **Operadores**.
    - Gestión de **Asignaciones**.
    - Gestión de **Usuarios** (restringido solo a Administradores).

## 2. Prevención de Inyección SQL (SQLi)

El sistema ha sido auditado para garantizar que todas las interacciones con la base de datos sean seguras.

### Medidas de seguridad:
- **Sentencias Preparadas (PDO):** Se confirmó y reforzó el uso de sentencias preparadas en todo el proyecto. Al usar marcadores de posición (`?` o `:nombre`), el motor de la base de datos trata los datos del usuario como texto plano, imposibilitando la ejecución de comandos maliciosos.
- **Validación de Tipos:** Se implementaron conversiones de tipo explícitas (ej. `(int)$id`) para parámetros numéricos.

## 3. Protección CSRF (Cross-Site Request Forgery)

Se implementó una defensa contra ataques CSRF para evitar que sitios maliciosos realicen acciones en nombre de un usuario autenticado.

### Implementación:
- **Tokens de Seguridad:** Cada formulario genera un token único almacenado en la sesión del usuario (`generar_csrf()`).
- **Validación en Backend:** Los scripts que procesan datos (`POST`) validan la existencia y coincidencia del token mediante `validar_csrf()`. Si el token falta o es incorrecto, la solicitud es rechazada inmediatamente.

## 4. Herramientas de Auditoría Incluidas

Se ha generado un script de prueba para verificar la seguridad de manera autónoma:

- **Archivo:** `security_test.php` (ubicado temporalmente en la carpeta de auditoría).
- **Función:** Intenta aplicar "payloads" comunes de inyección SQL contra la lógica del sistema para confirmar que son bloqueados por las capas de seguridad implementadas.

---

> **Nota de Seguridad:** Se recomienda mantener el archivo `helpers.php` y las configuraciones de `PDO` actualizadas para mantener este nivel de protección.
