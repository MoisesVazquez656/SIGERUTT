<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM rutas WHERE id_ruta = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: ../ver_rutas.php?mensaje=eliminado');
    exit();
} else {
    header('Location: ../ver_rutas.php');
    exit();
}
?>
