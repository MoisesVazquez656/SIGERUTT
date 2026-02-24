<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = strtoupper(trim($_POST['placa']));
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    // Tabla de capacidades
    $capacidades = [
        "Camión de carga ligera" => "1,500 kg",
        "Camión de carga pesada" => "10 toneladas",
        "Tráiler" => "20 toneladas",
        "Torton" => "15 toneladas",
        "Rabón" => "8 toneladas",
        "Caja seca" => "10 toneladas",
        "Refrigerado" => "12 toneladas",
        "Plataforma" => "25 toneladas",
        "Camioneta tipo van" => "800 kg",
        "Motocicleta de reparto" => "150 kg"
    ];

    if (empty($placa) || empty($tipo) || empty($estado)) {
        header('Location: ../registrar_vehiculo.php?mensaje=campos');
        exit;
    }

    if (strlen($placa) !== 9) {
        header('Location: ../registrar_vehiculo.php?mensaje=campos');
        exit;
    }

    if (!array_key_exists($tipo, $capacidades)) {
        header('Location: ../registrar_vehiculo.php?mensaje=campos');
        exit;
    }

    $capacidad = $capacidades[$tipo];

    $sql_check = "SELECT * FROM vehiculos WHERE LOWER(placa) = LOWER(?)";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$placa]);

    if ($stmt_check->fetch()) {
        header('Location: ../registrar_vehiculo.php?mensaje=placa_repetida');
        exit;
    }

    $sql = "INSERT INTO vehiculos (placa, tipo, capacidad, estado) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$placa, $tipo, $capacidad, $estado])) {
        header('Location: ../registrar_vehiculo.php?mensaje=exito');
        exit;
    } else {
        header('Location: ../registrar_vehiculo.php?mensaje=error');
        exit;
    }
} else {
    header('Location: ../registrar_vehiculo.php');
    exit;
}
?>
