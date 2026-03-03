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

if (isset($_POST['id_asignacion'], $_POST['id_ruta'], $_POST['id_vehiculo'], $_POST['id_operador'], $_POST['fecha_asignacion'])) {
    $id_asignacion = $_POST['id_asignacion'];
    $id_ruta = $_POST['id_ruta'];
    $id_vehiculo = $_POST['id_vehiculo'];
    $id_operador = $_POST['id_operador'];
    $fecha_asignacion = $_POST['fecha_asignacion'];

    $sql = "UPDATE asignaciones SET id_ruta = :id_ruta, id_vehiculo = :id_vehiculo, id_operador = :id_operador, fecha_asignacion = :fecha_asignacion WHERE id_asignacion = :id";
    $stmt = $conexion->prepare($sql);

    if (
        $stmt->execute([
            'id_ruta' => $id_ruta,
            'id_vehiculo' => $id_vehiculo,
            'id_operador' => $id_operador,
            'fecha_asignacion' => $fecha_asignacion,
            'id' => $id_asignacion
        ])
    ) {
        responder($esAjax, 'editar_asignacion.php?id=' . $id_asignacion . '&mensaje=actualizado', 'ok', 'Asignación actualizada correctamente.');
    } else {
        responder($esAjax, 'editar_asignacion.php?id=' . $id_asignacion, 'error', 'Error al actualizar la asignación.');
    }
} else {
    responder($esAjax, '../ver_asignaciones.php', 'error', 'Faltan datos obligatorios.');
}
?>