<?php include 'header.php'; ?>

<h2>Cambiar Contraseña</h2>

<form id="formCambiarContrasena" action="php/cambiar_contrasena.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= generar_csrf() ?>">

    <label for="contrasena_actual">Contraseña actual:</label>
    <input type="password" name="contrasena_actual" id="contrasena_actual" required>

    <label for="contrasena_nueva">Nueva contraseña:</label>
    <input type="password" name="contrasena_nueva" id="contrasena_nueva" required minlength="6">

    <label for="contrasena_confirmar">Confirmar nueva contraseña:</label>
    <input type="password" name="contrasena_confirmar" id="contrasena_confirmar" required minlength="6">

    <button type="submit">Cambiar contraseña</button>
</form>

<a href="perfil.php" class="boton-regresar">Regresar a Mi Perfil</a>

<?php include 'footer.php'; ?>
