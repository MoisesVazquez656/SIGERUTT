<?php
include 'header.php';
require 'php/conexion.php';

$id = $_SESSION['id_usuario'];
$sql = "SELECT id_usuario, nombre, correo FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo '<div class="mensaje error">No se pudo cargar el perfil.</div>';
    include 'footer.php';
    exit;
}
?>

<h2>Editar Mi Perfil</h2>

<form id="formEditarPerfil" action="php/actualizar_perfil.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= generar_csrf() ?>">

    <label for="nombre">Nombre completo:</label>
    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

    <button type="submit">Guardar cambios</button>
</form>

<a href="perfil.php" class="boton-regresar">Regresar a Mi Perfil</a>

<?php include 'footer.php'; ?>
