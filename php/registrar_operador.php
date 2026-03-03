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
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $licencia = filter_var($_POST['licencia'], FILTER_SANITIZE_STRING);
    $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);
    $disponibilidad = $_POST['disponibilidad'];

    if (empty($nombre) || empty($licencia) || empty($telefono) || empty($disponibilidad)) {
        responder($esAjax, '../registrar_operador.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
    }

    // Verificar que la licencia no esté registrada (sin importar mayúsculas)
    $sql_check = "SELECT * FROM operadores WHERE LOWER(licencia) = LOWER(?)";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$licencia]);

    if ($stmt_check->fetch()) {
        responder($esAjax, '../registrar_operador.php?mensaje=licencia_repetida', 'error', 'La licencia ya está registrada.');
    }

    $sql = "INSERT INTO operadores (nombre, licencia, telefono, disponibilidad) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $licencia, $telefono, $disponibilidad])) {
        responder($esAjax, '../registrar_operador.php?mensaje=exito', 'ok', 'Operador registrado correctamente.');
    } else {
        responder($esAjax, '../registrar_operador.php?mensaje=error', 'error', 'Error al registrar el operador.');
    }
} else {
    responder($esAjax, '../registrar_operador.php', 'error', 'Método no permitido.');
}
?>