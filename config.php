<?php
/**
 * SIGERUTT - Configuración base
 * - Configura sesión segura.
 * - Inicia sesión.
 * - Define BASE_URL de forma estable según el nombre de la carpeta del proyecto.
 */

define('SESSION_TIMEOUT', 1800); // 30 minutos de inactividad

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

$projectFolder = basename(__DIR__);
define('BASE_URL', '/' . $projectFolder . '/');

// URL de la API de usuarios (Flask). En Docker se define via variable de
// entorno API_BASE_URL (ver docker-compose.yml); fuera de Docker cae a localhost.
$apiUrl = getenv('API_BASE_URL');
define('API_BASE_URL', ($apiUrl !== false && $apiUrl !== '') ? rtrim($apiUrl, '/') : 'http://localhost:5000');
