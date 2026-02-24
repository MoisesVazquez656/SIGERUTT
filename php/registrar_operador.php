<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $licencia = filter_var($_POST['licencia'], FILTER_SANITIZE_STRING);
    $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);
    $disponibilidad = $_POST['disponibilidad'];

    if (empty($nombre) || empty($licencia) || empty($telefono) || empty($disponibilidad)) {
        header('Location: ../registrar_operador.php?mensaje=campos');
        exit;
    }

    // Verificar que la licencia no esté registrada (sin importar mayúsculas)
    $sql_check = "SELECT * FROM operadores WHERE LOWER(licencia) = LOWER(?)";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$licencia]);

    if ($stmt_check->fetch()) {
        header('Location: ../registrar_operador.php?mensaje=licencia_repetida');
        exit;
    }

    $sql = "INSERT INTO operadores (nombre, licencia, telefono, disponibilidad) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $licencia, $telefono, $disponibilidad])) {
        header('Location: ../registrar_operador.php?mensaje=exito');
        exit;
    } else {
        header('Location: ../registrar_operador.php?mensaje=error');
        exit;
    }
} else {
    header('Location: ../registrar_operador.php');
    exit;
}
?>
