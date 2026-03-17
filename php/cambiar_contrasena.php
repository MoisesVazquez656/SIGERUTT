<?php
require_once __DIR__ . '/../helpers.php';
require_login();
require_once __DIR__ . '/conexion.php';

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
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'Método no permitido.');
}

if (!validar_csrf()) {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'Token de seguridad inválido. Recarga la página.');
}

$id_usuario = (int) $_SESSION['id_usuario'];
$actual = $_POST['contrasena_actual'] ?? '';
$nueva = $_POST['contrasena_nueva'] ?? '';
$confirmar = $_POST['contrasena_confirmar'] ?? '';

if ($actual === '' || $nueva === '' || $confirmar === '') {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'Todos los campos son obligatorios.');
}

if (strlen($nueva) < 6) {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'La nueva contraseña debe tener al menos 6 caracteres.');
}

if ($nueva !== $confirmar) {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'Las contraseñas nuevas no coinciden.');
}

// Obtener contraseña actual de BD
$stmt = $conexion->prepare("SELECT contraseña FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($actual, $usuario['contraseña'])) {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'La contraseña actual es incorrecta.');
}

if (password_verify($nueva, $usuario['contraseña'])) {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'La nueva contraseña no puede ser igual a la actual.');
}

$hash = password_hash($nueva, PASSWORD_DEFAULT);
$stmt = $conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?");

if ($stmt->execute([$hash, $id_usuario])) {
    session_destroy();
    responder($esAjax, BASE_URL . 'login.php', 'ok', 'Contraseña cambiada correctamente.');
} else {
    responder($esAjax, BASE_URL . 'cambiar_contrasena.php', 'error', 'Error al cambiar la contraseña.');
}