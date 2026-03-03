# Usuarios

## 1. Descripción General
Este módulo permite a los administradores del sistema gestionar las cuentas de usuario de SIGERUTT. Es la puerta de entrada para definir quién puede acceder al sistema y qué acciones puede realizar según su rol asignado.

## 2. Acceso al Módulo
### Ruta de navegación
```
Menú Principal > Usuarios
```

### Permisos necesarios
- **Administrador**: Acceso total para crear, editar y eliminar usuarios.
- **Supervisor / Operador**: Acceso limitado según configuración de pantalla.

## 3. Pantalla de Listado
En la pantalla de "Usuarios Registrados" se visualiza una tabla con la información básica de cada registro:
- **Nombre**: Nombre completo del usuario.
- **Correo**: Correo electrónico registrado para el acceso.
- **Rol**: Perfil del usuario (Administrador, Supervisor u Operador).

### Acciones Disponibles:
- **Editar**: Permite modificar los datos del usuario.
- **Eliminar**: Remueve permanentemente al usuario del sistema (Acción irreversible).

---

## 4. Registro de Nuevo Usuario
Para registrar un nuevo usuario:
1. Haga clic en **Registrar Usuario** desde el Menú Principal o en el enlace correspondiente en la barra de navegación.
2. Complete los siguientes campos:
   - **Nombre completo**: Nombre y apellidos del usuario.
   - **Correo**: Email de contacto (debe ser único en el sistema).
   - **Contraseña**: Debe tener al menos 6 caracteres. Por seguridad, no se muestra el texto conforme lo escribe.
   - **Rol**: Seleccione una opción de la lista (Admin, Supervisor, Operador).
3. Presione el botón **Registrar Usuario**.

### Mensajes de Confirmación:
- **Éxito**: "Usuario registrado correctamente."
- **Alerta**: "El correo/nombre ya está registrado" or "La contraseña debe tener al menos 6 caracteres".

## 5. Edición de Usuario
1. Localice al usuario en el listado (`ver_usuarios.php`).
2. Haga clic en el enlace **Editar**.
3. Modifique el nombre, correo o rol según sea necesario.
4. Presione **Actualizar Usuario** para guardar los cambios.

---

## 6. Errores Comunes
| Mensaje | Causa | Solución |
|---------|-------|----------|
| "El correo ya está registrado" | Intento de usar un email que ya pertenece a otro usuario. | Use un correo electrónico diferente. |
| "Todos los campos son obligatorios" | Uno o más campos se dejaron vacíos. | Complete toda la información solicitada en el formulario. |
| "Error al registrar el usuario" | Fallo de conexión o problema interno del servidor. | Intente de nuevo más tarde o contacte al soporte técnico. |
