<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/../api_client.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function responder($esAjax, $redirectUrl, $status, $mensaje)
{
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'mensaje' => $mensaje]);
        exit;
    }
    header('Location: ' . $redirectUrl);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!validar_csrf()) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php', 'error', 'Token de seguridad inválido. Recarga la página.');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';
    $rol = trim($_POST['rol'] ?? '');

    if ($nombre === '' || $correo === '' || $contraseña === '' || $rol === '') {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
    }

    if (!valid_email($correo)) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=error', 'error', 'Correo no válido.');
    }

    if (strlen($contraseña) < 6) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=contraseña', 'error', 'La contraseña debe tener al menos 6 caracteres.');
    }

    $respuesta = api_admin_crear_usuario($_SESSION['api_token'] ?? '', $nombre, $correo, $contraseña, $rol);

    if ($respuesta['ok']) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=exito', 'ok', 'Usuario registrado correctamente.');
    }

    if ($respuesta['status'] === 401) {
        responder($esAjax, BASE_URL . 'login.php?mensaje=server', 'error', 'Sesión expirada. Inicia sesión nuevamente.');
    }

    if ($respuesta['status'] === 409) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=correo_repetido', 'error', 'Ese correo ya está registrado.');
    }

    responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=error', 'error', 'No se pudo registrar el usuario. Intenta nuevamente.');
} else {
    responder($esAjax, BASE_URL . 'registrar_usuario.php', 'error', 'Método no permitido.');
}
