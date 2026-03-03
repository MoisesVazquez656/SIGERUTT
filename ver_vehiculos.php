<?php include 'header.php'; ?>
<?php require 'php/conexion.php'; ?>

<h2>Vehículos Registrados</h2>

<?php
$sql = "SELECT * FROM vehiculos";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($vehiculos) > 0): ?>
    <table>
        <tr>
            <th>Placa</th>
            <th>Tipo</th>
            <th>Capacidad</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($vehiculos as $vehiculo): ?>
            <tr>
                <td><?php echo htmlspecialchars($vehiculo['placa']); ?></td>
                <td><?php echo htmlspecialchars($vehiculo['tipo']); ?></td>
                <td><?php echo htmlspecialchars($vehiculo['capacidad']); ?></td>
                <td><?php echo htmlspecialchars($vehiculo['estado']); ?></td>
                <td>
                    <a href="javascript:void(0);" class="accion-eliminar"
                        data-url="php/eliminar_vehiculo.php?id=<?php echo $vehiculo['id_vehiculo']; ?>">Eliminar</a>
                    <a href="php/editar_vehiculo.php?id=<?php echo $vehiculo['id_vehiculo']; ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="mensaje alerta">No hay vehículos registrados.</div>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>