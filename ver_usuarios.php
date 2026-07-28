<?php
require_once __DIR__ . '/helpers.php';
require_admin();
require_once __DIR__ . '/api_client.php';
include 'header.php';

$respuesta = api_admin_listar_usuarios($_SESSION['api_token'] ?? '');

if ($respuesta['status'] === 401) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$usuarios = $respuesta['ok'] ? ($respuesta['body']['usuarios'] ?? []) : [];
?>

<h2>Usuarios Registrados</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'eliminado'): ?>
    <div class="mensaje exito">Usuario eliminado correctamente.</div>
<?php endif; ?>

<?php if (!$respuesta['ok']): ?>
    <div class="mensaje error">No se pudo obtener la lista de usuarios.</div>
<?php elseif (count($usuarios) > 0): ?>
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
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['role']); ?></td>
                <td>
                    <a href="javascript:void(0);" class="accion-eliminar" data-url="php/eliminar_usuario.php?id=<?php echo urlencode($usuario['email']); ?>">Eliminar</a>
                    <a href="php/editar_usuario.php?id=<?php echo urlencode($usuario['email']); ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="mensaje alerta">No hay usuarios registrados.</div>
<?php endif; ?>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>