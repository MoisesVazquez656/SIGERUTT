<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/../api_client.php';

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Método no permitido.');
}

if (!validar_csrf()) {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Token de seguridad inválido. Recarga la página.');
}

if (isset($_POST['correo_actual'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $correoActual = trim($_POST['correo_actual']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = trim($_POST['rol']);

    $respuesta = api_admin_actualizar_usuario($_SESSION['api_token'] ?? '', $correoActual, [
        'nombre' => $nombre,
        'email' => $correo,
        'role' => $rol,
    ]);

    if ($respuesta['status'] === 401) {
        responder($esAjax, '../login.php', 'error', 'Sesión expirada. Inicia sesión nuevamente.');
    }

    if ($respuesta['ok']) {
        responder($esAjax, 'editar_usuario.php?id=' . urlencode($correo) . '&mensaje=actualizado', 'ok', 'Usuario actualizado correctamente.');
    }

    responder($esAjax, 'editar_usuario.php?id=' . urlencode($correoActual), 'error', 'No se pudo actualizar el usuario.');
} else {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Datos incompletos.');
}
