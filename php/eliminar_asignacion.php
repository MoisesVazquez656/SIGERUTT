<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM asignaciones WHERE id_asignacion = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: ../ver_asignaciones.php?mensaje=eliminado');
    exit();
} else {
    header('Location: ../ver_asignaciones.php');
    exit();
}
?>
