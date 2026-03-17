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
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'Método no permitido.');
}

if (!validar_csrf()) {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'Token de seguridad inválido. Recarga la página.');
}

$id_usuario = (int) $_SESSION['id_usuario'];
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');

if ($nombre === '' || $correo === '') {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'Todos los campos son obligatorios.');
}

if (!valid_email($correo)) {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'Correo no válido.');
}

// Verificar duplicado de nombre (excluyendo al usuario actual)
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE LOWER(nombre) = LOWER(?) AND id_usuario != ?");
$stmt->execute([$nombre, $id_usuario]);
if ($stmt->fetch()) {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'El nombre completo ya está registrado por otro usuario.');
}

// Verificar duplicado de correo (excluyendo al usuario actual)
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE LOWER(correo) = LOWER(?) AND id_usuario != ?");
$stmt->execute([$correo, $id_usuario]);
if ($stmt->fetch()) {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'El correo ya está registrado por otro usuario.');
}

$sql = "UPDATE usuarios SET nombre = ?, correo = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$nombre, $correo, $id_usuario])) {
    $_SESSION['nombre'] = $nombre;
    responder($esAjax, BASE_URL . 'perfil.php?mensaje=actualizado', 'ok', 'Perfil actualizado correctamente.');
} else {
    responder($esAjax, BASE_URL . 'editar_perfil.php', 'error', 'Error al actualizar el perfil.');
}
