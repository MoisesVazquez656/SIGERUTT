<?php
require 'conexion.php';

if (isset($_POST['id_operador'], $_POST['nombre'], $_POST['licencia'], $_POST['telefono'], $_POST['disponibilidad'])) {
    $id_operador = $_POST['id_operador'];
    $nombre = $_POST['nombre'];
    $licencia = $_POST['licencia'];
    $telefono = $_POST['telefono'];
    $disponibilidad = $_POST['disponibilidad'];

    $sql = "UPDATE operadores SET nombre = :nombre, licencia = :licencia, telefono = :telefono, disponibilidad = :disponibilidad WHERE id_operador = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'licencia' => $licencia,
        'telefono' => $telefono,
        'disponibilidad' => $disponibilidad,
        'id' => $id_operador
    ]);

    header('Location: editar_operador.php?id=' . $id_operador . '&mensaje=actualizado');
    exit();
} else {
    header('Location: ../ver_operadores.php');
    exit();
}
?>
