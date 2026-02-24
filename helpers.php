<?php
require_once __DIR__ . '/config.php';

/** Validación de email */
function valid_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/** Requiere sesión iniciada */
function require_login(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

/** Requiere rol admin (sin redirecciones para evitar bucles) */
function require_admin(): void {
    require_login();
    $rol = $_SESSION['rol'] ?? '';
    if ($rol !== 'admin') {
        http_response_code(403);
        echo '403 - Acceso denegado';
        exit;
    }
}
