<?php include 'header.php'; ?>
<?php require 'php/conexion.php'; ?>

<h2>Operadores Registrados</h2>

<?php
$sql = "SELECT * FROM operadores";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$operadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($operadores) > 0): ?>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Licencia</th>
            <th>Teléfono</th>
            <th>Disponibilidad</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($operadores as $operador): ?>
            <tr>
                <td><?php echo htmlspecialchars($operador['nombre']); ?></td>
                <td><?php echo htmlspecialchars($operador['licencia']); ?></td>
                <td><?php echo htmlspecialchars($operador['telefono']); ?></td>
                <td><?php echo htmlspecialchars($operador['disponibilidad']); ?></td>
                <td>
                    <a href="javascript:void(0);" class="accion-eliminar"
                        data-url="php/eliminar_operador.php?id=<?php echo $operador['id_operador']; ?>">Eliminar</a>
                    <a href="php/editar_operador.php?id=<?php echo $operador['id_operador']; ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="mensaje alerta">No hay operadores registrados.</div>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>