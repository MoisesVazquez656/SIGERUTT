<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../api_client.php';

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

$respuesta = api_login($correo, $contraseña);

if ($respuesta['ok'] && isset($respuesta['body']['token'])) {
    $body = $respuesta['body'];

    session_regenerate_id(true);
    $_SESSION['id_usuario'] = $body['id'];
    $_SESSION['nombre'] = $body['nombre'];
    $_SESSION['correo'] = $correo;
    $_SESSION['rol'] = $body['role'];
    $_SESSION['api_token'] = $body['token'];

    responder($esAjax, BASE_URL . 'index.php', 'ok', 'Inicio de sesión exitoso.');
}

if ($respuesta['status'] === 0) {
    responder($esAjax, BASE_URL . 'login.php?mensaje=server', 'error', 'Error de conexión con el servidor. Intenta nuevamente.');
}

responder($esAjax, BASE_URL . 'login.php?mensaje=error', 'error', 'Correo o contraseña incorrectos.');
