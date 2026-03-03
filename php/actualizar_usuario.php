<?php
require 'conexion.php';

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

if (isset($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol WHERE id_usuario = :id";
    $stmt = $conexion->prepare($sql);

    if (
        $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'rol' => $rol,
            'id' => $id_usuario
        ])
    ) {
        responder($esAjax, 'editar_usuario.php?id=' . $id_usuario . '&mensaje=actualizado', 'ok', 'Usuario actualizado correctamente.');
    } else {
        responder($esAjax, 'editar_usuario.php?id=' . $id_usuario, 'error', 'Error al actualizar el usuario.');
    }
} else {
    responder($esAjax, '../ver_usuarios.php', 'error', 'Faltan datos obligatorios.');
}
?>