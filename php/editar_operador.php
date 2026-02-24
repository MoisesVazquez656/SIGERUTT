<?php
require 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: ../ver_operadores.php');
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM operadores WHERE id_operador = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id' => $id]);
$operador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operador) {
    header('Location: ../ver_operadores.php');
    exit();
}
?>

<?php include '../header.php'; ?>

<h2>Editar Operador</h2>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="mensaje exito">Operador actualizado correctamente.</div>
<?php endif; ?>

<form action="actualizar_operador.php" method="POST" id="formOperador">
    <input type="hidden" name="id_operador" value="<?php echo $operador['id_operador']; ?>">

    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($operador['nombre']); ?>" required>

    <label>Licencia:</label>
    <input type="text" name="licencia" value="<?php echo htmlspecialchars($operador['licencia']); ?>" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="<?php echo htmlspecialchars($operador['telefono']); ?>" required>

    <label>Disponibilidad:</label>
    <select name="disponibilidad" required>
        <option value="disponible" <?php if ($operador['disponibilidad'] == 'disponible') echo 'selected'; ?>>Disponible</option>
        <option value="no disponible" <?php if ($operador['disponibilidad'] == 'no disponible') echo 'selected'; ?>>No Disponible</option>
    </select>

    <button type="submit">Actualizar Operador</button>
</form>

<a href="../ver_operadores.php" class="boton-regresar">Regresar al Menú Anterior</a>

<script src="../js/scripts.js"></script>
<?php include '../footer.php'; ?>
