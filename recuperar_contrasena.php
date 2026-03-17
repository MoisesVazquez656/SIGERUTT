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
    <title>Recuperar Contraseña - SIGERUTT</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="contenido">
        <h2>Recuperar Contraseña</h2>
        <p style="text-align:center; max-width:420px; margin:0 auto 20px;">
            Ingresa tu correo electrónico. Si existe una cuenta asociada, recibirás un enlace para restablecer tu contraseña.
        </p>

        <div id="mensaje-ajax"></div>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje <?= $mensaje === 'enviado' ? 'exito' : 'alerta' ?>">
                <?php
                if ($mensaje === 'enviado') {
                    echo 'Si el correo está registrado, recibirás un enlace de recuperación.';
                } elseif ($mensaje === 'error') {
                    echo 'Ocurrió un error. Intenta nuevamente.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form id="formRecuperar" action="<?= BASE_URL ?>php/enviar_recuperacion.php" method="POST">
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <button type="submit">Enviar enlace de recuperación</button>
        </form>

        <a href="<?= BASE_URL ?>login.php">Volver a Iniciar Sesión</a>
    </div>

    <script src="<?= BASE_URL ?>js/scripts.js"></script>

</body>

</html>
