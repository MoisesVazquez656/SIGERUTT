<?php include 'header.php'; ?>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="mensaje <?php echo ($_GET['mensaje'] == 'exito') ? 'exito' : 'error'; ?>">
        <?php
        if ($_GET['mensaje'] == 'exito') {
            echo 'Vehículo registrado correctamente.';
        } elseif ($_GET['mensaje'] == 'error') {
            echo 'Error al registrar el vehículo.';
        } elseif ($_GET['mensaje'] == 'placa_repetida') {
            echo 'La placa ya está registrada.';
        } elseif ($_GET['mensaje'] == 'campos') {
            echo 'Todos los campos son obligatorios.';
        }
        ?>
    </div>
<?php endif; ?>

<h2>Registrar Vehículo</h2>

<form id="formVehiculo" action="php/registrar_vehiculo.php" method="POST">
    <label for="placa">Placa (AB-345-CD):</label>
    <input type="text" name="placa" id="placa" maxlength="9" required><br><br>

    <label for="tipo">Tipo de Vehículo:</label>
    <select name="tipo" id="tipo" required>
        <option value="">Selecciona un tipo</option>
        <option value="Camión de carga ligera">Camión de carga ligera</option>
        <option value="Camión de carga pesada">Camión de carga pesada</option>
        <option value="Tráiler">Tráiler</option>
        <option value="Torton">Torton</option>
        <option value="Rabón">Rabón</option>
        <option value="Caja seca">Caja seca</option>
        <option value="Refrigerado">Refrigerado</option>
        <option value="Plataforma">Plataforma</option>
        <option value="Camioneta tipo van">Camioneta tipo van</option>
        <option value="Motocicleta de reparto">Motocicleta de reparto</option>
    </select><br><br>

    <label for="capacidad">Capacidad:</label>
    <input type="text" name="capacidad" id="capacidad" readonly><br><br>

    <label for="estado">Estado:</label>
    <select name="estado" required>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select><br><br>

    <button type="submit">Registrar Vehículo</button>
</form>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>