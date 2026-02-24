<?php
require_once __DIR__ . '/helpers.php';
require_admin();
include 'header.php';
?>

<?php if (isset($_GET['mensaje'])): ?>
    <?php
    $tipoMensaje = '';
    $textoMensaje = '';

    if ($_GET['mensaje'] == 'exito') {
        $tipoMensaje = 'exito';
        $textoMensaje = 'Usuario registrado correctamente.';
    } elseif ($_GET['mensaje'] == 'error') {
        $tipoMensaje = 'error';
        $textoMensaje = 'Error al registrar el usuario.';
    } elseif ($_GET['mensaje'] == 'campos') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'Todos los campos son obligatorios.';
    } elseif ($_GET['mensaje'] == 'contraseña') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($_GET['mensaje'] == 'nombre_repetido') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'El nombre completo ya está registrado.';
    } elseif ($_GET['mensaje'] == 'correo_repetido') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'El correo ya está registrado.';
    }
    ?>
    <div class="mensaje <?php echo $tipoMensaje; ?>">
        <?php echo $textoMensaje; ?>
    </div>
<?php endif; ?>

<h2>Registrar Usuario - SIGERUTT</h2>

<form id="formUsuario" action="php/registrar_usuario.php" method="POST">
    <label for="nombre">Nombre completo:</label>
    <input type="text" name="nombre" id="nombre" required><br><br>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" required><br><br>

    <label for="contraseña">Contraseña:</label>
    <input type="password" name="contraseña" required minlength="6"><br><br>

    <label for="rol">Rol:</label>
    <select name="rol" required>
        <option value="">Selecciona un rol</option>
        <option value="admin">Administrador</option>
        <option value="supervisor">Supervisor</option>
        <option value="operador">Operador</option>
    </select><br><br>

    <button type="submit">Registrar Usuario</button>
</form>

<br>
<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<script src="js/scripts.js"></script>

<?php include 'footer.php'; ?>
