<?php include 'header.php'; ?>

<h2>Bienvenido, <?php echo $_SESSION['nombre']; ?> (Rol: <?php echo $_SESSION['rol']; ?>)</h2>

<div class="menu-container">
    <div class="menu-card">
        <a href="registrar_usuario.php">
            <i class="fas fa-user-plus"></i>
            <span>Registrar Usuario</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="registrar_ruta.php">
            <i class="fas fa-route"></i>
            <span>Registrar Nueva Ruta</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="registrar_vehiculo.php">
            <i class="fas fa-truck"></i>
            <span>Registrar Vehículo</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="registrar_operador.php">
            <i class="fas fa-id-badge"></i>
            <span>Registrar Operador</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="asignaciones.php">
            <i class="fas fa-tasks"></i>
            <span>Asignar Rutas</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="ver_usuarios.php">
            <i class="fas fa-users"></i>
            <span>Ver Usuarios</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="ver_rutas.php">
            <i class="fas fa-map-marked-alt"></i>
            <span>Ver Rutas</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="ver_vehiculos.php">
            <i class="fas fa-truck-moving"></i>
            <span>Ver Vehículos</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="ver_operadores.php">
            <i class="fas fa-id-card"></i>
            <span>Ver Operadores</span>
        </a>
    </div>

    <div class="menu-card">
        <a href="ver_asignaciones.php">
            <i class="fas fa-list"></i>
            <span>Ver Asignaciones</span>
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>
