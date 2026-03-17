<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexion.php';

// Limpiar session_id en BD antes de destruir la sesión
if (!empty($_SESSION['id_usuario'])) {
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET session_id = NULL WHERE id_usuario = ?");
        $stmt->execute([$_SESSION['id_usuario']]);
    } catch (Throwable $e) {
        // No bloquear el logout si falla la consulta
    }
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ' . BASE_URL . 'login.php');
exit;
