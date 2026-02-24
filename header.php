<?php
require_once __DIR__ . '/helpers.php';
require_login();

$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol    = $_SESSION['rol'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGERUTT</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script defer src="<?= BASE_URL ?>js/scripts.js"></script>
</head>
<body>

<!-- Header superior (título + buscador + usuario + iconos) -->
<header class="header header-top">
    <h1>SIGERUTT - Sistema de Gestión de Rutas de Transporte Terrestre</h1>

    <div class="header-right">
        <form class="header-search" action="<?= BASE_URL ?>buscar.php" method="GET" onsubmit="return validarBusquedaHeader && validarBusquedaHeader();">
            <input type="text" name="q" id="q" placeholder="Buscar..." minlength="2">
            <button type="submit" title="Buscar"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="header-user">
            <div class="header-user-name"><?= htmlspecialchars($nombre) ?></div>
            <div class="header-user-role">(Rol: <?= htmlspecialchars($rol) ?>)</div>
        </div>

        <nav class="header-icons">
            <a href="<?= BASE_URL ?>index.php" title="Inicio"><i class="fa-solid fa-house"></i></a>
            <a href="<?= BASE_URL ?>php/logout.php" title="Cerrar sesión"><i class="fa-solid fa-right-from-bracket"></i></a>
        </nav>
    </div>
</header>

<!-- Barra de navegación horizontal (solo las opciones que pediste) -->
<nav class="nav-bar">
    <a href="<?= BASE_URL ?>sistemap.php" style="margin-top:0;">Mapa del sitio</a>
    <?php if (($rol ?? '') === 'admin'): ?>
        <a href="<?= BASE_URL ?>registrar_usuario.php">Nuevo usuario</a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>registrar_ruta_dinamica.php">Nueva ruta</a>
    <a href="<?= BASE_URL ?>registrar_vehiculo.php">Nuevo vehículo</a>
    <a href="<?= BASE_URL ?>registrar_operador.php">Nuevo operador</a>
    <a href="<?= BASE_URL ?>asignaciones.php">Asignar ruta</a>

    <?php if (($rol ?? '') === 'admin'): ?>
        <a href="<?= BASE_URL ?>ver_usuarios.php">Usuarios</a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>ver_rutas.php">Rutas</a>
    <a href="<?= BASE_URL ?>ver_vehiculos.php">Vehículos</a>
    <a href="<?= BASE_URL ?>ver_operadores.php">Operadores</a>
    <a href="<?= BASE_URL ?>ver_asignacion.php">Asignaciones</a>
</nav>

<main class="contenido">
