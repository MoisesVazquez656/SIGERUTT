<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: ../ver_usuarios.php?mensaje=eliminado');
    exit();
} else {
    header('Location: ../ver_usuarios.php');
    exit();
}
?>
