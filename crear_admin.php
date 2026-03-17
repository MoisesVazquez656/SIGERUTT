<?php
/**
 * SIGERUTT - Crear usuario administrador inicial
 * 
 * SEGURIDAD: Elimina este archivo despues de crear tu usuario admin.
 */

require_once __DIR__ . '/php/db_config.php';

$mensajes = [];
$error = false;

try {
    $conexion = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Ejecutar migracion: agregar columnas si no existen
    $columnas_nuevas = [
        'session_id' => 'VARCHAR(128) DEFAULT NULL',
        'token_recuperacion' => 'VARCHAR(64) DEFAULT NULL',
        'token_expira' => 'DATETIME DEFAULT NULL',
    ];

    $stmt = $conexion->query("SHOW COLUMNS FROM usuarios");
    $existentes = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');

    foreach ($columnas_nuevas as $col => $tipo) {
        if (!in_array($col, $existentes, true)) {
            $conexion->exec("ALTER TABLE usuarios ADD COLUMN {$col} {$tipo}");
            $mensajes[] = "Columna '{$col}' agregada correctamente.";
        } else {
            $mensajes[] = "Columna '{$col}' ya existe, omitida.";
        }
    }

    // Verificar si ya existe un admin
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->execute(['admin@sigerutt.com']);

    if ($stmt->fetch()) {
        $mensajes[] = "El usuario admin@sigerutt.com ya existe. No se creo duplicado.";
    } else {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['Administrador', 'admin@sigerutt.com', $hash, 'admin']);
        $mensajes[] = "Usuario admin creado exitosamente.";
    }
} catch (Throwable $e) {
    $error = true;
    $mensajes[] = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Admin - SIGERUTT</title>
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #a0a0a0; margin: 0; padding: 40px; }
        .card { max-width: 520px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,.2); }
        h2 { text-align: center; color: #FF7F32; }
        .msg { padding: 10px 14px; margin: 8px 0; border-radius: 6px; font-weight: bold; }
        .msg.ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg.err { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .creds { background: #f7f7f7; padding: 14px; border-radius: 8px; border-left: 4px solid #FF7F32; margin: 16px 0; }
        .warn { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 12px; border-radius: 6px; margin-top: 16px; text-align: center; font-weight: bold; }
        a { display: block; text-align: center; margin-top: 20px; color: #FF7F32; font-weight: bold; text-decoration: none; }
        a:hover { color: #FFA64D; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2>SIGERUTT - Crear Admin</h2>

        <?php foreach ($mensajes as $m): ?>
            <div class="msg <?= $error ? 'err' : 'ok' ?>"><?= htmlspecialchars($m) ?></div>
        <?php endforeach; ?>

        <?php if (!$error): ?>
            <div class="creds">
                <p><strong>Correo:</strong> admin@sigerutt.com</p>
                <p><strong>Contrasena:</strong> admin123</p>
                <p><strong>Rol:</strong> admin</p>
            </div>

            <div class="warn">
                ELIMINA ESTE ARCHIVO (crear_admin.php) despues de iniciar sesion.
            </div>

            <a href="login.php">Ir a Iniciar Sesion</a>
        <?php endif; ?>
    </div>
</body>
</html>
