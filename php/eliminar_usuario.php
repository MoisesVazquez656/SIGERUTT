<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/../api_client.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (isset($_GET['id']) && trim($_GET['id']) !== '') {
    $email = $_GET['id'];

    $respuesta = api_admin_eliminar_usuario($_SESSION['api_token'] ?? '', $email);

    if ($respuesta['status'] === 401) {
        if ($esAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'mensaje' => 'Sesión expirada. Inicia sesión nuevamente.']);
            exit;
        }
        header('Location: ../login.php');
        exit();
    }

    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'ok', 'mensaje' => 'Usuario eliminado correctamente.']);
        exit;
    }
    header('Location: ../ver_usuarios.php?mensaje=eliminado');
    exit();
} else {
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'mensaje' => 'ID no proporcionado.']);
        exit;
    }
    header('Location: ../ver_usuarios.php');
    exit();
}
