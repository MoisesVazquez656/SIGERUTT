<?php
require 'conexion.php';

if (isset($_POST['id_vehiculo'], $_POST['placa'], $_POST['tipo'], $_POST['capacidad'], $_POST['estado'])) {
    $id_vehiculo = $_POST['id_vehiculo'];
    $placa = strtoupper($_POST['placa']);
    $tipo = $_POST['tipo'];
    $capacidad = $_POST['capacidad'];
    $estado = $_POST['estado'];

    $sql = "UPDATE vehiculos SET placa = :placa, tipo = :tipo, capacidad = :capacidad, estado = :estado WHERE id_vehiculo = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'placa' => $placa,
        'tipo' => $tipo,
        'capacidad' => $capacidad,
        'estado' => $estado,
        'id' => $id_vehiculo
    ]);

    header('Location: editar_vehiculo.php?id=' . $id_vehiculo . '&mensaje=actualizado');
    exit();
} else {
    header('Location: ../ver_vehiculos.php');
    exit();
}
?>
