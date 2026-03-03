<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/conexion.php';

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';
    $rol = trim($_POST['rol'] ?? '');

    // Validar campos vacíos
    if ($nombre === '' || $correo === '' || $contraseña === '' || $rol === '') {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=campos', 'error', 'Todos los campos son obligatorios.');
    }

    if (!valid_email($correo)) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=error', 'error', 'Correo no válido.');
    }

    // Validar longitud mínima de contraseña
    if (strlen($contraseña) < 6) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=contraseña', 'error', 'La contraseña debe tener al menos 6 caracteres.');
    }

    // Verificar si el nombre completo ya existe (sin importar mayúsculas)
    $sql_nombre = "SELECT * FROM usuarios WHERE LOWER(nombre) = LOWER(?)";
    $stmt_nombre = $conexion->prepare($sql_nombre);
    $stmt_nombre->execute([$nombre]);

    if ($stmt_nombre->fetch()) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=nombre_repetido', 'error', 'El nombre completo ya está registrado.');
    }

    // Verificar si el correo ya existe (sin importar mayúsculas)
    $sql_correo = "SELECT * FROM usuarios WHERE LOWER(correo) = LOWER(?)";
    $stmt_correo = $conexion->prepare($sql_correo);
    $stmt_correo->execute([$correo]);

    if ($stmt_correo->fetch()) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=correo_repetido', 'error', 'El correo ya está registrado.');
    }

    // Cifrar contraseña
    $contraseña_segura = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql_insert = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);

    if ($stmt_insert->execute([$nombre, $correo, $contraseña_segura, $rol])) {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=exito', 'ok', 'Usuario registrado correctamente.');
    } else {
        responder($esAjax, BASE_URL . 'registrar_usuario.php?mensaje=error', 'error', 'Error al registrar el usuario.');
    }
} else {
    responder($esAjax, BASE_URL . 'registrar_usuario.php', 'error', 'Método no permitido.');
}
?>