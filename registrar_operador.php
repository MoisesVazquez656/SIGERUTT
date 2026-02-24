<?php include 'header.php'; ?>

<?php if (isset($_GET['mensaje'])): ?>
    <?php
    $tipoMensaje = '';
    $textoMensaje = '';

    if ($_GET['mensaje'] == 'exito') {
        $tipoMensaje = 'exito';
        $textoMensaje = 'Operador registrado correctamente.';
    } elseif ($_GET['mensaje'] == 'error') {
        $tipoMensaje = 'error';
        $textoMensaje = 'Error al registrar el operador.';
    } elseif ($_GET['mensaje'] == 'campos') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'Todos los campos son obligatorios.';
    } elseif ($_GET['mensaje'] == 'licencia_repetida') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'La licencia ya está registrada.';
    }
    ?>
    <div class="mensaje <?php echo $tipoMensaje; ?>">
        <?php echo $textoMensaje; ?>
    </div>
<?php endif; ?>

<h2>Registrar Operador</h2>

<form id="formOperador" action="php/registrar_operador.php" method="POST">
    <label for="nombre">Nombre completo:</label>
    <input type="text" name="nombre" required><br><br>

    <label for="licencia">Número de Licencia:</label>
    <input type="text" name="licencia" required><br><br>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" required><br><br>

    <label for="disponibilidad">Disponibilidad:</label>
    <select name="disponibilidad" required>
        <option value="disponible">Disponible</option>
        <option value="no disponible">No Disponible</option>
    </select><br><br>

    <button type="submit">Registrar Operador</button>
</form>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<script src="js/scripts.js"></script>

<?php include 'footer.php'; ?>
