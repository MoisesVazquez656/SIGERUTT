<?php
require_once __DIR__ . '/config.php';

// Si ya hay sesión, manda al menú
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
            <div class="mensaje <?php echo ($mensaje === 'error' || $mensaje === 'server') ? 'error' : 'alerta'; ?>">
                <?php
                if ($mensaje === 'error') {
                    echo 'Usuario o contraseña incorrectos.';
                } elseif ($mensaje === 'campos') {
                    echo 'Todos los campos son obligatorios.';
                } elseif ($mensaje === 'email') {
                    echo 'Correo no válido.';
                } elseif ($mensaje === 'server') {
                    echo 'Error del servidor. Intenta nuevamente.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form id="formLogin" action="<?= BASE_URL ?>php/login.php" method="POST">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="contraseña">contraseña:</label>
            <input type="password" name="contraseña" id="contraseña" required>

            <button type="submit">Iniciar sesión</button>
        </form>
    </div>

    <script src="js/scripts.js"></script>

</body>

</html>