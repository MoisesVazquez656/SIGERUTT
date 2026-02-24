<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';
    $rol = trim($_POST['rol'] ?? '');

    // Validar campos vacíos
    if ($nombre === '' || $correo === '' || $contraseña === '' || $rol === '') {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=campos');
        exit;
    }

    if (!valid_email($correo)) {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=error');
        exit;
    }

    // Validar longitud mínima de contraseña
    if (strlen($contraseña) < 6) {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=contraseña');
        exit;
    }

    // Verificar si el nombre completo ya existe (sin importar mayúsculas)
    $sql_nombre = "SELECT * FROM usuarios WHERE LOWER(nombre) = LOWER(?)";
    $stmt_nombre = $conexion->prepare($sql_nombre);
    $stmt_nombre->execute([$nombre]);

    if ($stmt_nombre->fetch()) {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=nombre_repetido');
        exit;
    }

    // Verificar si el correo ya existe (sin importar mayúsculas)
    $sql_correo = "SELECT * FROM usuarios WHERE LOWER(correo) = LOWER(?)";
    $stmt_correo = $conexion->prepare($sql_correo);
    $stmt_correo->execute([$correo]);

    if ($stmt_correo->fetch()) {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=correo_repetido');
        exit;
    }

    // Cifrar contraseña
    $contraseña_segura = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql_insert = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);

    if ($stmt_insert->execute([$nombre, $correo, $contraseña_segura, $rol])) {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=exito');
        exit;
    } else {
        header('Location: ' . BASE_URL . 'registrar_usuario.php?mensaje=error');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'registrar_usuario.php');
    exit;
}
?>
