<?php
require 'conexion.php';

if (isset($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol WHERE id_usuario = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'correo' => $correo,
        'rol' => $rol,
        'id' => $id_usuario
    ]);

    header('Location: editar_usuario.php?id=' . $id_usuario . '&mensaje=actualizado');
    exit();
} else {
    header('Location: ../ver_usuarios.php');
    exit();
}
?>
