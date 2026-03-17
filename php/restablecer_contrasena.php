<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexion.php';

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

$token = $_POST['token'] ?? '';
$nueva = $_POST['contrasena_nueva'] ?? '';
$confirmar = $_POST['contrasena_confirmar'] ?? '';

if ($token === '' || $nueva === '' || $confirmar === '') {
    responder($esAjax, BASE_URL . 'restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=error', 'error', 'Todos los campos son obligatorios.');
}

if (strlen($nueva) < 6) {
    responder($esAjax, BASE_URL . 'restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=corta', 'error', 'La contraseña debe tener al menos 6 caracteres.');
}

if ($nueva !== $confirmar) {
    responder($esAjax, BASE_URL . 'restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=no_coinciden', 'error', 'Las contraseñas no coinciden.');
}

// Verificar token válido y no expirado
$stmt = $conexion->prepare(
    "SELECT id_usuario FROM usuarios WHERE token_recuperacion = ? AND token_expira > NOW() LIMIT 1"
);
$stmt->execute([$token]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    responder($esAjax, BASE_URL . 'recuperar_contrasena.php', 'error', 'El enlace de recuperación es inválido o ha expirado.');
}

$hash = password_hash($nueva, PASSWORD_DEFAULT);

// Actualizar contraseña, limpiar token e invalidar sesiones
$stmt = $conexion->prepare(
    "UPDATE usuarios SET contraseña = ?, token_recuperacion = NULL, token_expira = NULL, session_id = NULL WHERE id_usuario = ?"
);

if ($stmt->execute([$hash, $usuario['id_usuario']])) {
    responder($esAjax, BASE_URL . 'login.php?mensaje=contrasena_restablecida', 'ok', 'Contraseña restablecida correctamente. Inicia sesión con tu nueva contraseña.');
} else {
    responder($esAjax, BASE_URL . 'restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=error', 'error', 'Error al restablecer la contraseña.');
}
