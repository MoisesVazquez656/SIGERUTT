<?php
include 'header.php';
require 'php/conexion.php';

// Consultar rutas
$sql_rutas = "SELECT * FROM rutas ORDER BY nombre_ruta ASC";
$stmt_rutas = $conexion->prepare($sql_rutas);
$stmt_rutas->execute();
$rutas = $stmt_rutas->fetchAll(PDO::FETCH_ASSOC);

// Consultar vehículos activos
$sql_vehiculos = "SELECT * FROM vehiculos WHERE estado = 'activo' ORDER BY placa ASC";
$stmt_vehiculos = $conexion->prepare($sql_vehiculos);
$stmt_vehiculos->execute();
$vehiculos = $stmt_vehiculos->fetchAll(PDO::FETCH_ASSOC);

// Consultar operadores disponibles
$sql_operadores = "SELECT * FROM operadores WHERE disponibilidad = 'disponible' ORDER BY nombre ASC";
$stmt_operadores = $conexion->prepare($sql_operadores);
$stmt_operadores->execute();
$operadores = $stmt_operadores->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_GET['mensaje'])): ?>
    <?php
    $tipoMensaje = '';
    $textoMensaje = '';

    if ($_GET['mensaje'] == 'exito') {
        $tipoMensaje = 'exito';
        $textoMensaje = 'Ruta asignada correctamente.';
    } elseif ($_GET['mensaje'] == 'error') {
        $tipoMensaje = 'error';
        $textoMensaje = 'Error al asignar la ruta.';
    } elseif ($_GET['mensaje'] == 'campos') {
        $tipoMensaje = 'alerta';
        $textoMensaje = 'Todos los campos son obligatorios.';
    }
    ?>
    <div class="mensaje <?php echo $tipoMensaje; ?>">
        <?php echo $textoMensaje; ?>
    </div>
<?php endif; ?>

<h2>Asignar Ruta a Vehículo y Operador</h2>

<form id="formAsignacion" action="php/asignaciones.php" method="POST">
    <label for="id_ruta">Ruta:</label>
    <select name="id_ruta" required>
        <option value="">Selecciona una ruta</option>
        <?php foreach ($rutas as $ruta) { ?>
            <option value="<?php echo $ruta['id_ruta']; ?>"><?php echo htmlspecialchars($ruta['nombre_ruta']); ?></option>
        <?php } ?>
    </select><br><br>

    <label for="id_vehiculo">Vehículo:</label>
    <select name="id_vehiculo" required>
        <option value="">Selecciona un vehículo</option>
        <?php foreach ($vehiculos as $vehiculo) { ?>
            <option value="<?php echo $vehiculo['id_vehiculo']; ?>"><?php echo htmlspecialchars($vehiculo['placa']); ?></option>
        <?php } ?>
    </select><br><br>

    <label for="id_operador">Operador:</label>
    <select name="id_operador" required>
        <option value="">Selecciona un operador</option>
        <?php foreach ($operadores as $operador) { ?>
            <option value="<?php echo $operador['id_operador']; ?>"><?php echo htmlspecialchars($operador['nombre']); ?></option>
        <?php } ?>
    </select><br><br>

    <label for="fecha_asignacion">Fecha de Asignación:</label>
    <input type="date" name="fecha_asignacion" required><br><br>

    <button type="submit">Asignar Ruta</button>
</form>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<script src="js/scripts.js"></script>

<?php include 'footer.php'; ?>
