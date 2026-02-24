<?php
require 'conexion.php';

if (isset($_POST['id_asignacion'], $_POST['id_ruta'], $_POST['id_vehiculo'], $_POST['id_operador'], $_POST['fecha_asignacion'])) {
    $id_asignacion = $_POST['id_asignacion'];
    $id_ruta = $_POST['id_ruta'];
    $id_vehiculo = $_POST['id_vehiculo'];
    $id_operador = $_POST['id_operador'];
    $fecha_asignacion = $_POST['fecha_asignacion'];

    $sql = "UPDATE asignaciones SET id_ruta = :id_ruta, id_vehiculo = :id_vehiculo, id_operador = :id_operador, fecha_asignacion = :fecha_asignacion WHERE id_asignacion = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'id_ruta' => $id_ruta,
        'id_vehiculo' => $id_vehiculo,
        'id_operador' => $id_operador,
        'fecha_asignacion' => $fecha_asignacion,
        'id' => $id_asignacion
    ]);

    header('Location: editar_asignacion.php?id=' . $id_asignacion . '&mensaje=actualizado');
    exit();
} else {
    header('Location: ../ver_asignaciones.php');
    exit();
}
?>
