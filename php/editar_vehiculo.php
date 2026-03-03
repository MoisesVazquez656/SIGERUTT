<?php
require 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: ../ver_vehiculos.php');
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM vehiculos WHERE id_vehiculo = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id' => $id]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    header('Location: ../ver_vehiculos.php');
    exit();
}
?>

<?php include '../header.php'; ?>

<h2>Editar Vehículo</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Vehículo actualizado correctamente.</div>
<?php endif; ?>

<form action="actualizar_vehiculo.php" method="POST" id="formVehiculo">
    <input type="hidden" name="id_vehiculo" value="<?php echo $vehiculo['id_vehiculo']; ?>">

    <label>Placa:</label>
    <input type="text" id="placa" name="placa" value="<?php echo htmlspecialchars($vehiculo['placa']); ?>" required>

    <label>Tipo:</label>
    <select name="tipo" id="tipo" required>
        <option value="">Seleccione un tipo</option>
        <option value="Camión de carga ligera" <?php if ($vehiculo['tipo'] == 'Camión de carga ligera')
            echo 'selected'; ?>>Camión de carga ligera</option>
        <option value="Camión de carga pesada" <?php if ($vehiculo['tipo'] == 'Camión de carga pesada')
            echo 'selected'; ?>>Camión de carga pesada</option>
        <option value="Tráiler" <?php if ($vehiculo['tipo'] == 'Tráiler')
            echo 'selected'; ?>>Tráiler</option>
        <option value="Torton" <?php if ($vehiculo['tipo'] == 'Torton')
            echo 'selected'; ?>>Torton</option>
        <option value="Rabón" <?php if ($vehiculo['tipo'] == 'Rabón')
            echo 'selected'; ?>>Rabón</option>
        <option value="Caja seca" <?php if ($vehiculo['tipo'] == 'Caja seca')
            echo 'selected'; ?>>Caja seca</option>
        <option value="Refrigerado" <?php if ($vehiculo['tipo'] == 'Refrigerado')
            echo 'selected'; ?>>Refrigerado</option>
        <option value="Plataforma" <?php if ($vehiculo['tipo'] == 'Plataforma')
            echo 'selected'; ?>>Plataforma</option>
        <option value="Camioneta tipo van" <?php if ($vehiculo['tipo'] == 'Camioneta tipo van')
            echo 'selected'; ?>>
            Camioneta tipo van</option>
        <option value="Motocicleta de reparto" <?php if ($vehiculo['tipo'] == 'Motocicleta de reparto')
            echo 'selected'; ?>>Motocicleta de reparto</option>
    </select>

    <label>Capacidad:</label>
    <input type="text" name="capacidad" id="capacidad" value="<?php echo htmlspecialchars($vehiculo['capacidad']); ?>"
        readonly required>

    <label>Estado:</label>
    <select name="estado" required>
        <option value="activo" <?php if ($vehiculo['estado'] == 'activo')
            echo 'selected'; ?>>Activo</option>
        <option value="inactivo" <?php if ($vehiculo['estado'] == 'inactivo')
            echo 'selected'; ?>>Inactivo</option>
    </select>

    <button type="submit">Actualizar Vehículo</button>
</form>

<a href="../ver_vehiculos.php" class="boton-regresar">Regresar al Menú Anterior</a>

<?php include '../footer.php'; ?>