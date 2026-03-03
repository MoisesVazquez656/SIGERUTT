<?php
require 'conexion.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function responder($esAjax, $redirectUrl, $status, $mensaje)
{
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'mensaje' => $mensaje]);
        exit;
    }
    header('Location: ' . $redirectUrl);
    exit;
}

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
        responder($esAjax, '../registrar_vehiculo.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
    }

    if (strlen($placa) !== 9) {
        responder($esAjax, '../registrar_vehiculo.php?mensaje=campos', 'error', 'La placa debe tener exactamente 9 caracteres.');
    }

    if (!array_key_exists($tipo, $capacidades)) {
        responder($esAjax, '../registrar_vehiculo.php?mensaje=campos', 'error', 'Tipo de vehículo no válido.');
    }

    $capacidad = $capacidades[$tipo];

    $sql_check = "SELECT * FROM vehiculos WHERE LOWER(placa) = LOWER(?)";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$placa]);

    if ($stmt_check->fetch()) {
        responder($esAjax, '../registrar_vehiculo.php?mensaje=placa_repetida', 'error', 'La placa ya está registrada.');
    }

    $sql = "INSERT INTO vehiculos (placa, tipo, capacidad, estado) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$placa, $tipo, $capacidad, $estado])) {
        responder($esAjax, '../registrar_vehiculo.php?mensaje=exito', 'ok', 'Vehículo registrado correctamente.');
    } else {
        responder($esAjax, '../registrar_vehiculo.php?mensaje=error', 'error', 'Error al registrar el vehículo.');
    }
} else {
    responder($esAjax, '../registrar_vehiculo.php', 'error', 'Método no permitido.');
}
?>