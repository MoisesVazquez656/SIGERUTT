<?php include 'header.php'; ?>
<?php require 'php/conexion.php'; ?>

<h2>Usuarios Registrados</h2>

<?php
$sql = "SELECT * FROM usuarios";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($usuarios) > 0): ?>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                <td>
                    <a href="javascript:void(0);" class="accion-eliminar"
                        data-url="php/eliminar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>">Eliminar</a>
                    <a href="php/editar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="mensaje alerta">No hay usuarios registrados.</div>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>