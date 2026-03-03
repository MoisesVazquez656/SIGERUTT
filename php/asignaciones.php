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
    $id_ruta = $_POST['id_ruta'];
    $id_vehiculo = $_POST['id_vehiculo'];
    $id_operador = $_POST['id_operador'];
    $fecha_asignacion = $_POST['fecha_asignacion'];

    if (empty($id_ruta) || empty($id_vehiculo) || empty($id_operador) || empty($fecha_asignacion)) {
        responder($esAjax, '../asignaciones.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
    }

    $sql = "INSERT INTO asignaciones (id_ruta, id_vehiculo, id_operador, fecha_asignacion) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$id_ruta, $id_vehiculo, $id_operador, $fecha_asignacion])) {
        responder($esAjax, '../asignaciones.php?mensaje=exito', 'ok', 'Ruta asignada correctamente.');
    } else {
        responder($esAjax, '../asignaciones.php?mensaje=error', 'error', 'Error al asignar la ruta.');
    }
} else {
    responder($esAjax, '../asignaciones.php', 'error', 'Método no permitido.');
}
?>