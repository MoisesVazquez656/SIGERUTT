<?php
require_once __DIR__ . '/config.php';

/** Validación de email */
function valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/** Genera (si no existe) y devuelve un token CSRF almacenado en sesión */
function generar_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Valida el token CSRF recibido por POST contra el de sesión */
function validar_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Requiere sesión iniciada.
 * Verifica timeout por inactividad y sesión única contra BD.
 */
function require_login(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }

    // Timeout por inactividad
    if (isset($_SESSION['ultima_actividad'])) {
        $inactivo = time() - $_SESSION['ultima_actividad'];
        if ($inactivo > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login.php?mensaje=timeout');
            exit;
        }
    }
    $_SESSION['ultima_actividad'] = time();

    // Verificación de sesión única contra BD
    verificar_sesion_unica();
}

/** Requiere rol admin */
function require_admin(): void
{
    require_login();
    $rol = $_SESSION['rol'] ?? '';
    if ($rol !== 'admin') {
        http_response_code(403);
        include __DIR__ . '/error/403.php';
        exit;
    }
}

/** Requiere uno de los roles especificados */
function require_role(string ...$roles): void
{
    require_login();
    if (!in_array($_SESSION['rol'] ?? '', $roles, true)) {
        http_response_code(403);
        include __DIR__ . '/error/403.php';
        exit;
    }
}

/**
 * Compara el session_id actual con el almacenado en BD.
 * Si no coincide, la sesión fue invalidada por otro inicio de sesión.
 * Usa su propia conexión para no interferir con require_once de conexion.php en handlers.
 */
function verificar_sesion_unica(): void
{
    static $verificado = false;
    if ($verificado) {
        return;
    }
    $verificado = true;

    try {
        require_once __DIR__ . '/php/db_config.php';
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->prepare(
            "SELECT session_id FROM usuarios WHERE id_usuario = ? LIMIT 1"
        );
        $stmt->execute([$_SESSION['id_usuario']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || ($row['session_id'] !== null && $row['session_id'] !== session_id())) {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login.php?mensaje=sesion_cerrada');
            exit;
        }
    } catch (Throwable $e) {
        // Si falla la consulta, no bloquear al usuario
    }
}
