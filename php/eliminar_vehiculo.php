<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM vehiculos WHERE id_vehiculo = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: ../ver_vehiculos.php?mensaje=eliminado');
    exit();
} else {
    header('Location: ../ver_vehiculos.php');
    exit();
}
?>
