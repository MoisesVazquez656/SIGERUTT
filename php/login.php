<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$contraseña = $_POST['contraseña'] ?? '';

if ($correo === '' || $contraseña === '') {
    header('Location: ' . BASE_URL . 'login.php?mensaje=campos');
    exit;
}

if (!valid_email($correo)) {
    header('Location: ' . BASE_URL . 'login.php?mensaje=email');
    exit;
}

try {
    $sql = "SELECT id_usuario, nombre, correo, contraseña, rol
            FROM usuarios
            WHERE LOWER(correo) = LOWER(?)
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }

    header('Location: ' . BASE_URL . 'login.php?mensaje=error');
    exit;
} catch (Throwable $e) {
    header('Location: ' . BASE_URL . 'login.php?mensaje=server');
    exit;
}
