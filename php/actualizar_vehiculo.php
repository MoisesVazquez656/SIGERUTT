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

if (isset($_POST['id_vehiculo'], $_POST['placa'], $_POST['tipo'], $_POST['capacidad'], $_POST['estado'])) {
    $id_vehiculo = $_POST['id_vehiculo'];
    $placa = strtoupper($_POST['placa']);
    $tipo = $_POST['tipo'];
    $capacidad = $_POST['capacidad'];
    $estado = $_POST['estado'];

    $sql = "UPDATE vehiculos SET placa = :placa, tipo = :tipo, capacidad = :capacidad, estado = :estado WHERE id_vehiculo = :id";
    $stmt = $conexion->prepare($sql);

    if (
        $stmt->execute([
            'placa' => $placa,
            'tipo' => $tipo,
            'capacidad' => $capacidad,
            'estado' => $estado,
            'id' => $id_vehiculo
        ])
    ) {
        responder($esAjax, 'editar_vehiculo.php?id=' . $id_vehiculo . '&mensaje=actualizado', 'ok', 'Vehículo actualizado correctamente.');
    } else {
        responder($esAjax, 'editar_vehiculo.php?id=' . $id_vehiculo, 'error', 'Error al actualizar el vehículo.');
    }
} else {
    responder($esAjax, '../ver_vehiculos.php', 'error', 'Faltan datos obligatorios.');
}
?>