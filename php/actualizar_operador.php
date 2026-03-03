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

if (isset($_POST['id_operador'], $_POST['nombre'], $_POST['licencia'], $_POST['telefono'], $_POST['disponibilidad'])) {
    $id_operador = $_POST['id_operador'];
    $nombre = $_POST['nombre'];
    $licencia = $_POST['licencia'];
    $telefono = $_POST['telefono'];
    $disponibilidad = $_POST['disponibilidad'];

    $sql = "UPDATE operadores SET nombre = :nombre, licencia = :licencia, telefono = :telefono, disponibilidad = :disponibilidad WHERE id_operador = :id";
    $stmt = $conexion->prepare($sql);

    if (
        $stmt->execute([
            'nombre' => $nombre,
            'licencia' => $licencia,
            'telefono' => $telefono,
            'disponibilidad' => $disponibilidad,
            'id' => $id_operador
        ])
    ) {
        responder($esAjax, 'editar_operador.php?id=' . $id_operador . '&mensaje=actualizado', 'ok', 'Operador actualizado correctamente.');
    } else {
        responder($esAjax, 'editar_operador.php?id=' . $id_operador, 'error', 'Error al actualizar el operador.');
    }
} else {
    responder($esAjax, '../ver_operadores.php', 'error', 'Faltan datos obligatorios.');
}
?>