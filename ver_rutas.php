<?php include 'header.php'; ?>
<?php require 'php/conexion.php'; ?>

<h2>Rutas Registradas</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'registrado'): ?>
    <div class="mensaje exito">Ruta registrada correctamente.</div>
<?php elseif (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Ruta actualizada correctamente.</div>
<?php endif; ?>

<?php
$sql = "SELECT * FROM rutas";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($rutas) > 0): ?>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Distancia Total</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($rutas as $ruta): ?>
            <tr>
                <td><?php echo htmlspecialchars($ruta['nombre_ruta']); ?></td>

                <!-- Mostramos distancia total -->
                <td><?php echo isset($ruta['distancia_total']) ? $ruta['distancia_total'] . ' km' : 'No calculada'; ?></td>

                <td>
                    <a href="javascript:void(0);" class="accion-eliminar"
                        data-url="php/eliminar_ruta.php?id=<?php echo $ruta['id_ruta']; ?>">Eliminar</a>
                    <a href="editar_ruta_dinamica.php?id=<?php echo $ruta['id_ruta']; ?>">Editar</a>
                    <a href="ver_mapa.php?id=<?php echo $ruta['id_ruta']; ?>&origen=ver_rutas.php">Ver Mapa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No hay rutas registradas.</p>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>