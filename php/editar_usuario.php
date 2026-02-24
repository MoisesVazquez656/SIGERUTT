<?php
require 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: ../ver_usuarios.php');
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id_usuario = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: ../ver_usuarios.php');
    exit();
}
?>

<?php include '../header.php'; ?>

<h2>Editar Usuario</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Usuario actualizado correctamente.</div>
<?php endif; ?>

<form action="actualizar_usuario.php" method="POST" id="formUsuario">
    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">

    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

    <label>Correo:</label>
    <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

    <label>Rol:</label>
    <select name="rol" required>
        <option value="">Seleccione un rol</option>
        <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
        <option value="supervisor" <?php if ($usuario['rol'] == 'supervisor') echo 'selected'; ?>>Supervisor</option>
        <option value="operador" <?php if ($usuario['rol'] == 'operador') echo 'selected'; ?>>Operador</option>
    </select>

    <button type="submit">Actualizar Usuario</button>
</form>

<a href="../ver_usuarios.php" class="boton-regresar">Regresar al Menú Anterior</a>

<script src="../js/scripts.js"></script>
<?php include '../footer.php'; ?>
