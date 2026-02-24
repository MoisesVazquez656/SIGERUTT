<?php
// Mostrar errores solo en desarrollo (opcional)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Captura errores fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        http_response_code(500);
        header("Location: /SIGERUTT/error/500.php");
        exit;
    }
});

// Captura excepciones no controladas
set_exception_handler(function ($e) {
    http_response_code(500);
    header("Location: /SIGERUTT/error/500.php");
    exit;
});
