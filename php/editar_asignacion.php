<?php
require 'conexion.php';

// Verificar que exista el ID
if (!isset($_GET['id'])) {
    header('Location: ../ver_asignaciones.php');
    exit();
}

$id = $_GET['id'];

// Obtener datos de la asignación actual
$sql = "SELECT * FROM asignaciones WHERE id_asignacion = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id' => $id]);
$asignacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$asignacion) {
    header('Location: ../ver_asignaciones.php');
    exit();
}

// Obtener rutas disponibles
$sqlRutas = "SELECT * FROM rutas";
$stmtRutas = $conexion->prepare($sqlRutas);
$stmtRutas->execute();
$rutas = $stmtRutas->fetchAll(PDO::FETCH_ASSOC);

// Obtener vehículos disponibles
$sqlVehiculos = "SELECT * FROM vehiculos";
$stmtVehiculos = $conexion->prepare($sqlVehiculos);
$stmtVehiculos->execute();
$vehiculos = $stmtVehiculos->fetchAll(PDO::FETCH_ASSOC);

// Obtener operadores disponibles
$sqlOperadores = "SELECT * FROM operadores";
$stmtOperadores = $conexion->prepare($sqlOperadores);
$stmtOperadores->execute();
$operadores = $stmtOperadores->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../header.php'; ?>

<h2>Editar Asignación</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Asignación actualizada correctamente.</div>
<?php endif; ?>

<form action="actualizar_asignacion.php" method="POST" id="formAsignacion">
    <input type="hidden" name="id_asignacion" value="<?php echo $asignacion['id_asignacion']; ?>">

    <label>Ruta:</label>
    <select name="id_ruta" required>
        <option value="">Seleccione una ruta</option>
        <?php foreach ($rutas as $ruta): ?>
            <option value="<?php echo $ruta['id_ruta']; ?>" <?php if ($asignacion['id_ruta'] == $ruta['id_ruta']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($ruta['nombre_ruta']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Vehículo:</label>
    <select name="id_vehiculo" required>
        <option value="">Seleccione un vehículo</option>
        <?php foreach ($vehiculos as $vehiculo): ?>
            <option value="<?php echo $vehiculo['id_vehiculo']; ?>" <?php if ($asignacion['id_vehiculo'] == $vehiculo['id_vehiculo']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($vehiculo['placa']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Operador:</label>
    <select name="id_operador" required>
        <option value="">Seleccione un operador</option>
        <?php foreach ($operadores as $operador): ?>
            <option value="<?php echo $operador['id_operador']; ?>" <?php if ($asignacion['id_operador'] == $operador['id_operador']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($operador['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Fecha de Asignación:</label>
    <input type="date" name="fecha_asignacion" value="<?php echo $asignacion['fecha_asignacion']; ?>" required>

    <button type="submit">Actualizar Asignación</button>
</form>

<a href="javascript:window.history.back();" class="boton-regresar">Regresar al Menú Anterior</a>

<script src="../js/scripts.js"></script>
<?php include '../footer.php'; ?>
