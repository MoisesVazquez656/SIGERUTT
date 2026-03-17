<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder($esAjax, BASE_URL . 'recuperar_contrasena.php', 'error', 'Método no permitido.');
}

$correo = trim($_POST['correo'] ?? '');

if ($correo === '') {
    responder($esAjax, BASE_URL . 'recuperar_contrasena.php?mensaje=error', 'error', 'Debes ingresar un correo.');
}

// Mensaje genérico para no revelar si el correo existe
$mensajeGenerico = 'Si el correo está registrado, recibirás un enlace de recuperación en tu bandeja de entrada.';

$stmt = $conexion->prepare("SELECT id_usuario, nombre FROM usuarios WHERE LOWER(correo) = LOWER(?) LIMIT 1");
$stmt->execute([$correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // Responder igual para no exponer información
    responder($esAjax, BASE_URL . 'recuperar_contrasena.php?mensaje=enviado', 'ok', $mensajeGenerico);
}

// Generar token y guardar en BD
$token = bin2hex(random_bytes(32));
$expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

$stmt = $conexion->prepare(
    "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id_usuario = ?"
);
$stmt->execute([$token, $expira, $usuario['id_usuario']]);

// Construir enlace de recuperación
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$enlace = $protocolo . '://' . $host . BASE_URL . 'restablecer_contrasena.php?token=' . $token;

// Enviar email con PHPMailer
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_ENCRYPTION;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress($correo, $usuario['nombre']);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña - SIGERUTT';
    $mail->Body    = '
        <div style="font-family:Poppins,Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;">
            <h2 style="color:#FF7F32;text-align:center;">SIGERUTT</h2>
            <p>Hola <strong>' . htmlspecialchars($usuario['nombre']) . '</strong>,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace:</p>
            <p style="text-align:center;margin:30px 0;">
                <a href="' . $enlace . '" 
                   style="background-color:#FF7F32;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;">
                    Restablecer contraseña
                </a>
            </p>
            <p>Este enlace expirará en <strong>1 hora</strong>.</p>
            <p>Si no solicitaste este cambio, ignora este correo.</p>
            <hr style="border:none;border-top:1px solid #ddd;margin:30px 0;">
            <p style="font-size:12px;color:#888;text-align:center;">SIGERUTT - Sistema de Gestión de Rutas de Transporte Terrestre</p>
        </div>';
    $mail->AltBody = "Hola {$usuario['nombre']},\n\nPara restablecer tu contraseña, visita el siguiente enlace:\n{$enlace}\n\nEste enlace expirará en 1 hora.\n\nSi no solicitaste este cambio, ignora este correo.";

    $mail->send();
} catch (Exception $e) {
    // Log del error pero no revelar detalles al usuario
    error_log("Error al enviar correo de recuperación: " . $mail->ErrorInfo);
}

responder($esAjax, BASE_URL . 'recuperar_contrasena.php?mensaje=enviado', 'ok', $mensajeGenerico);
