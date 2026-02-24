<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ruta = $_POST['id_ruta'];
    $id_vehiculo = $_POST['id_vehiculo'];
    $id_operador = $_POST['id_operador'];
    $fecha_asignacion = $_POST['fecha_asignacion'];

    if (empty($id_ruta) || empty($id_vehiculo) || empty($id_operador) || empty($fecha_asignacion)) {
        header('Location: ../asignaciones.php?mensaje=campos');
        exit;
    }

    $sql = "INSERT INTO asignaciones (id_ruta, id_vehiculo, id_operador, fecha_asignacion) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$id_ruta, $id_vehiculo, $id_operador, $fecha_asignacion])) {
        header('Location: ../asignaciones.php?mensaje=exito');
        exit;
    } else {
        header('Location: ../asignaciones.php?mensaje=error');
        exit;
    }
} else {
    header('Location: ../asignaciones.php');
    exit;
}
?>
