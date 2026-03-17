<?php
include 'header.php';
require 'php/conexion.php';

$id = $_SESSION['id_usuario'];
$sql = "SELECT id_usuario, nombre, correo, rol FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo '<div class="mensaje error">No se pudo cargar el perfil.</div>';
    include 'footer.php';
    exit;
}

$roles = ['admin' => 'Administrador', 'supervisor' => 'Supervisor', 'operador' => 'Operador'];
$rolTexto = $roles[$usuario['rol']] ?? $usuario['rol'];
?>

<h2>Mi Perfil</h2>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="mensaje <?php
        echo in_array($_GET['mensaje'], ['actualizado', 'contrasena_cambiada'], true) ? 'exito' : 'alerta';
    ?>">
        <?php
        if ($_GET['mensaje'] === 'actualizado') {
            echo 'Perfil actualizado correctamente.';
        } elseif ($_GET['mensaje'] === 'contrasena_cambiada') {
            echo 'Contraseña cambiada correctamente.';
        }
        ?>
    </div>
<?php endif; ?>

<div class="perfil-card">
    <div class="perfil-avatar">
        <i class="fas fa-user-circle"></i>
    </div>
    <div class="perfil-info">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars($rolTexto) ?></p>
    </div>
    <div class="perfil-acciones">
        <a href="editar_perfil.php" class="boton-perfil"><i class="fas fa-edit"></i> Editar perfil</a>
        <a href="cambiar_contrasena.php" class="boton-perfil boton-secundario"><i class="fas fa-key"></i> Cambiar contraseña</a>
    </div>
</div>

<a href="index.php" class="boton-regresar">Regresar al Menú Principal</a>

<?php include 'footer.php'; ?>
