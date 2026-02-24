<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM operadores WHERE id_operador = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: ../ver_operadores.php?mensaje=eliminado');
    exit();
} else {
    header('Location: ../ver_operadores.php');
    exit();
}
?>
