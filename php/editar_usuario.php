<?php
require_once __DIR__ . '/../helpers.php';
require_admin();
require_once __DIR__ . '/../api_client.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    header('Location: ../ver_usuarios.php');
    exit();
}

$email = $_GET['id'];

$respuesta = api_admin_obtener_usuario($_SESSION['api_token'] ?? '', $email);

if ($respuesta['status'] === 401) {
    header('Location: ../login.php');
    exit();
}

if (!$respuesta['ok']) {
    header('Location: ../ver_usuarios.php');
    exit();
}

$usuario = $respuesta['body'];
?>

<?php include '../header.php'; ?>

<h2>Editar Usuario</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Usuario actualizado correctamente.</div>
<?php endif; ?>

<form action="actualizar_usuario.php" method="POST" id="formUsuario">
    <input type="hidden" name="csrf_token" value="<?= generar_csrf() ?>">
    <input type="hidden" name="correo_actual" value="<?php echo htmlspecialchars($usuario['email']); ?>">

    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

    <label>Correo:</label>
    <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

    <label>Rol:</label>
    <select name="rol" required>
        <option value="">Seleccione un rol</option>
        <option value="admin" <?php if ($usuario['role'] == 'admin') echo 'selected'; ?>>Administrador</option>
        <option value="supervisor" <?php if ($usuario['role'] == 'supervisor') echo 'selected'; ?>>Supervisor</option>
        <option value="operador" <?php if ($usuario['role'] == 'operador') echo 'selected'; ?>>Operador</option>
    </select>

    <button type="submit">Actualizar Usuario</button>
</form>

<a href="../ver_usuarios.php" class="boton-regresar">Regresar al Menú Anterior</a>

<?php include '../footer.php'; ?>