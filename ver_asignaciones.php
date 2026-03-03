<?php include 'header.php'; ?>
<?php require 'php/conexion.php'; ?>

<h2>Asignaciones Registradas</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'eliminado'): ?>
    <div class="mensaje exito">Asignación eliminada correctamente.</div>
<?php endif; ?>

<?php
$sql = "SELECT asignaciones.id_asignacion, rutas.id_ruta, rutas.nombre_ruta, vehiculos.placa, operadores.nombre, asignaciones.fecha_asignacion
FROM asignaciones
JOIN rutas ON asignaciones.id_ruta = rutas.id_ruta
JOIN vehiculos ON asignaciones.id_vehiculo = vehiculos.id_vehiculo
JOIN operadores ON asignaciones.id_operador = operadores.id_operador";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($asignaciones) > 0): ?>
    <table>
        <tr>
            <th>Ruta</th>
            <th>Vehículo</th>
            <th>Operador</th>
            <th>Fecha de Asignación</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($asignaciones as $asignacion): ?>
            <tr>
                <td><?php echo htmlspecialchars($asignacion['nombre_ruta']); ?></td>
                <td><?php echo htmlspecialchars($asignacion['placa']); ?></td>
                <td><?php echo htmlspecialchars($asignacion['nombre']); ?></td>
                <td><?php echo htmlspecialchars($asignacion['fecha_asignacion']); ?></td>
                <td>
                    <a href="javascript:void(0);" class="accion-eliminar"
                        data-url="php/eliminar_asignacion.php?id=<?php echo $asignacion['id_asignacion']; ?>">Eliminar</a>
                    <a href="php/editar_asignacion.php?id=<?php echo $asignacion['id_asignacion']; ?>">Editar</a>
                    <a href="ver_mapa.php?id=<?php echo $asignacion['id_ruta']; ?>&origen=ver_asignaciones.php">Ver Mapa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No hay asignaciones registradas.</p>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>