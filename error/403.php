<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado - SIGERUTT</title>
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/SIGERUTT/' ?>css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenido" style="text-align:center; padding-top:60px;">
        <i class="fas fa-ban" style="font-size:64px; color:#FF7F32; margin-bottom:20px;"></i>
        <h2>403 - Acceso Denegado</h2>
        <p>No tienes permisos para acceder a esta página.</p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/SIGERUTT/' ?>index.php" class="boton-regresar">Volver al inicio</a>
    </div>
</body>
</html>
