<?php
require 'conexion.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM rutas WHERE id_ruta = :id";
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute(['id' => $id]);

    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => $resultado ? 'ok' : 'error',
            'mensaje' => $resultado ? 'Ruta eliminada correctamente.' : 'Error al eliminar la ruta.'
        ]);
        exit;
    }

    header('Location: ../ver_rutas.php?mensaje=eliminado');
    exit();
} else {
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'mensaje' => 'ID no proporcionado.']);
        exit;
    }
    header('Location: ../ver_rutas.php');
    exit();
}
?>