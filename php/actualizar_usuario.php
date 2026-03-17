<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
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
    responder($esAjax, '../ver_usuarios.php', 'error', 'Método no permitido.');
}

if (!validar_csrf()) {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Token de seguridad inválido. Recarga la página.');
}

if (!isset($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Faltan datos obligatorios.');
}

$id_usuario = (int) $_POST['id_usuario'];
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$rol = trim($_POST['rol']);

if ($nombre === '' || $correo === '' || $rol === '') {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'Todos los campos son obligatorios.');
}

if (!valid_email($correo)) {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'Correo no válido.');
}

// Verificar duplicado de nombre (excluyendo al usuario actual)
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE LOWER(nombre) = LOWER(?) AND id_usuario != ?");
$stmt->execute([$nombre, $id_usuario]);
if ($stmt->fetch()) {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'El nombre ya está registrado por otro usuario.');
}

// Verificar duplicado de correo (excluyendo al usuario actual)
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE LOWER(correo) = LOWER(?) AND id_usuario != ?");
$stmt->execute([$correo, $id_usuario]);
if ($stmt->fetch()) {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'El correo ya está registrado por otro usuario.');
}

$sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol WHERE id_usuario = :id";
$stmt = $conexion->prepare($sql);

if ($stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'rol' => $rol, 'id' => $id_usuario])) {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario . '&mensaje=actualizado', 'ok', 'Usuario actualizado correctamente.');
} else {
    responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'Error al actualizar el usuario.');
}
