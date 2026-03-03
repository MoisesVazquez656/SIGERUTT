<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/conexion.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function responder($esAjax, $redirectUrl, $status, $mensaje)
{
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'mensaje' => $mensaje, 'redirect' => $redirectUrl]);
        exit;
    }
    header('Location: ' . $redirectUrl);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder($esAjax, BASE_URL . 'login.php', 'error', 'Método no permitido.');
}

$correo = trim($_POST['correo'] ?? '');
$contraseña = $_POST['contraseña'] ?? '';

if ($correo === '' || $contraseña === '') {
    responder($esAjax, BASE_URL . 'login.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
}

if (!valid_email($correo)) {
    responder($esAjax, BASE_URL . 'login.php?mensaje=email', 'error', 'Correo no válido.');
}

try {
    $sql = "SELECT id_usuario, nombre, correo, contraseña, rol
            FROM usuarios
            WHERE LOWER(correo) = LOWER(?)
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        responder($esAjax, BASE_URL . 'index.php', 'ok', 'Inicio de sesión exitoso.');
    }

    responder($esAjax, BASE_URL . 'login.php?mensaje=error', 'error', 'Usuario o contraseña incorrectos.');
} catch (Throwable $e) {
    responder($esAjax, BASE_URL . 'login.php?mensaje=server', 'error', 'Error del servidor. Intenta nuevamente.');
}
