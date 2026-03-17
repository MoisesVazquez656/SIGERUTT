<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/php/conexion.php';

$token = $_GET['token'] ?? '';
$valido = false;

if ($token !== '') {
    $stmt = $conexion->prepare(
        "SELECT id_usuario FROM usuarios WHERE token_recuperacion = ? AND token_expira > NOW() LIMIT 1"
    );
    $stmt->execute([$token]);
    $valido = (bool) $stmt->fetch();
}

$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - SIGERUTT</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="contenido">
        <h2>Restablecer Contraseña</h2>

        <div id="mensaje-ajax"></div>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje <?= $mensaje === 'error' ? 'error' : 'alerta' ?>">
                <?php
                if ($mensaje === 'error') {
                    echo 'Error al restablecer la contraseña.';
                } elseif ($mensaje === 'no_coinciden') {
                    echo 'Las contraseñas no coinciden.';
                } elseif ($mensaje === 'corta') {
                    echo 'La contraseña debe tener al menos 6 caracteres.';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$valido): ?>
            <div class="mensaje error">
                El enlace de recuperación es inválido o ha expirado.
            </div>
            <a href="<?= BASE_URL ?>recuperar_contrasena.php">Solicitar un nuevo enlace</a>
        <?php else: ?>
            <form id="formRestablecer" action="<?= BASE_URL ?>php/restablecer_contrasena.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <label for="contrasena_nueva">Nueva contraseña:</label>
                <input type="password" name="contrasena_nueva" id="contrasena_nueva" required minlength="6">

                <label for="contrasena_confirmar">Confirmar nueva contraseña:</label>
                <input type="password" name="contrasena_confirmar" id="contrasena_confirmar" required minlength="6">

                <button type="submit">Restablecer contraseña</button>
            </form>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>login.php">Volver a Iniciar Sesión</a>
    </div>

    <script src="<?= BASE_URL ?>js/scripts.js"></script>

</body>

</html>
