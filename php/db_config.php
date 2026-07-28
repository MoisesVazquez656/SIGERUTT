<?php
/**
 * SIGERUTT - Configuración de base de datos
 */
if (!defined('DB_HOST')) {
    // Los valores por defecto son los de un entorno local (XAMPP). En Docker se
    // sobreescriben con las variables de entorno definidas en docker-compose.yml.
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'sistema_rutas');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '1234');
}
