<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/../api_client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../ver_usuarios.php');
    exit();
}

if (!validar_csrf()) {
    header('Location: ../ver_usuarios.php');
    exit();
}

if (isset($_POST['correo_actual'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $correoActual = trim($_POST['correo_actual']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = trim($_POST['rol']);

    $respuesta = api_admin_actualizar_usuario($_SESSION['api_token'] ?? '', $correoActual, [
        'nombre' => $nombre,
        'email' => $correo,
        'role' => $rol,
    ]);

    if ($respuesta['status'] === 401) {
        header('Location: ../login.php');
        exit();
    }

    if ($respuesta['ok']) {
        header('Location: editar_usuario.php?id=' . urlencode($correo) . '&mensaje=actualizado');
        exit();
    }

    header('Location: editar_usuario.php?id=' . urlencode($correoActual));
    exit();
} else {
    header('Location: ../ver_usuarios.php');
    exit();
}
