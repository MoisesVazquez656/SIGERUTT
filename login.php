<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['id_usuario'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - SIGERUTT</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="contenido">
        <h2>Inicio de Sesión - SIGERUTT</h2>

        <div id="mensaje-ajax"></div>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje <?php
                echo in_array($mensaje, ['error', 'server'], true) ? 'error' : 'alerta';
            ?>">
                <?php
                if ($mensaje === 'error') {
                    echo 'Usuario o contraseña incorrectos.';
                } elseif ($mensaje === 'campos') {
                    echo 'Todos los campos son obligatorios.';
                } elseif ($mensaje === 'email') {
                    echo 'Correo no válido.';
                } elseif ($mensaje === 'server') {
                    echo 'Error del servidor. Intenta nuevamente.';
                } elseif ($mensaje === 'timeout') {
                    echo 'Tu sesión expiró por inactividad. Por favor, inicia sesión nuevamente.';
                } elseif ($mensaje === 'sesion_cerrada') {
                    echo 'Tu sesión fue cerrada porque se inició sesión en otro dispositivo.';
                } elseif ($mensaje === 'contrasena_restablecida') {
                    echo 'Contraseña restablecida correctamente. Inicia sesión con tu nueva contraseña.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form id="formLogin" action="<?= BASE_URL ?>php/login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generar_csrf() ?>">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" id="contraseña" required>

            <button type="submit">Iniciar sesión</button>
        </form>

        <a href="<?= BASE_URL ?>recuperar_contrasena.php">¿Olvidaste tu contraseña?</a>
    </div>

    <script src="<?= BASE_URL ?>js/scripts.js"></script>

</body>

</html>
