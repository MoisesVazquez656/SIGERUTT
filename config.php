<?php
/**
 * SIGERUTT - Configuración base
 * - Inicia sesión.
 * - Define BASE_URL de forma estable según el nombre de la carpeta del proyecto.
 *   Ej: si el proyecto está en htdocs/SIGERUTT => BASE_URL = /SIGERUTT/
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BASE_URL estable: /<carpeta_del_proyecto>/
$projectFolder = basename(__DIR__);
define('BASE_URL', '/' . $projectFolder . '/');
