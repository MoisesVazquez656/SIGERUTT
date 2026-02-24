<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/php/conexion.php';

$q = trim($_GET['q'] ?? '');
if ($q === '' || mb_strlen($q) < 2) {
    echo '<div class="mensaje alerta">Escribe al menos 2 caracteres para buscar.</div>';
    include 'footer.php';
    exit;
}

$like = '%' . $q . '%';

// Búsquedas sencillas (evita errores si no hay permisos)
$results = [
    'usuarios' => [],
    'rutas' => [],
    'vehiculos' => [],
    'operadores' => [],
];

try {
    // Usuarios (si eres admin)
    if (($_SESSION['rol'] ?? '') === 'admin') {
        $st = $conexion->prepare("SELECT id_usuario, nombre, correo, rol FROM usuarios WHERE nombre LIKE ? OR correo LIKE ? LIMIT 20");
        $st->execute([$like, $like]);
        $results['usuarios'] = $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rutas
    $st = $conexion->prepare("SELECT id_ruta, nombre_ruta, distancia_total FROM rutas WHERE nombre_ruta LIKE ? LIMIT 20");
    $st->execute([$like]);
    $results['rutas'] = $st->fetchAll(PDO::FETCH_ASSOC);

    // Vehículos
    $st = $conexion->prepare("SELECT id_vehiculo, placa, tipo, estado FROM vehiculos WHERE placa LIKE ? OR tipo LIKE ? LIMIT 20");
    $st->execute([$like, $like]);
    $results['vehiculos'] = $st->fetchAll(PDO::FETCH_ASSOC);

    // Operadores
    $st = $conexion->prepare("SELECT id_operador, nombre, licencia, disponibilidad FROM operadores WHERE nombre LIKE ? OR licencia LIKE ? LIMIT 20");
    $st->execute([$like, $like]);
    $results['operadores'] = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    echo '<div class="mensaje error">Error al buscar. Intenta nuevamente.</div>';
    include 'footer.php';
    exit;
}
?>

<h2>Resultados de búsqueda: "<?= htmlspecialchars($q) ?>"</h2>

<?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
    <h3>Usuarios</h3>
    <?php if (!$results['usuarios']): ?>
        <p style="text-align:center">Sin resultados.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th></tr>
            <?php foreach ($results['usuarios'] as $u): ?>
                <tr>
                    <td><?= (int)$u['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= htmlspecialchars($u['rol']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>

<h3>Rutas</h3>
<?php if (!$results['rutas']): ?>
    <p style="text-align:center">Sin resultados.</p>
<?php else: ?>
    <table>
        <tr><th>ID</th><th>Ruta</th><th>Distancia</th></tr>
        <?php foreach ($results['rutas'] as $r): ?>
            <tr>
                <td><?= (int)$r['id_ruta'] ?></td>
                <td><?= htmlspecialchars($r['nombre_ruta']) ?></td>
                <td><?= htmlspecialchars((string)$r['distancia_total']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h3>Vehículos</h3>
<?php if (!$results['vehiculos']): ?>
    <p style="text-align:center">Sin resultados.</p>
<?php else: ?>
    <table>
        <tr><th>ID</th><th>Placa</th><th>Tipo</th><th>Estado</th></tr>
        <?php foreach ($results['vehiculos'] as $v): ?>
            <tr>
                <td><?= (int)$v['id_vehiculo'] ?></td>
                <td><?= htmlspecialchars($v['placa']) ?></td>
                <td><?= htmlspecialchars($v['tipo']) ?></td>
                <td><?= htmlspecialchars($v['estado']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h3>Operadores</h3>
<?php if (!$results['operadores']): ?>
    <p style="text-align:center">Sin resultados.</p>
<?php else: ?>
    <table>
        <tr><th>ID</th><th>Nombre</th><th>Licencia</th><th>Disponibilidad</th></tr>
        <?php foreach ($results['operadores'] as $o): ?>
            <tr>
                <td><?= (int)$o['id_operador'] ?></td>
                <td><?= htmlspecialchars($o['nombre']) ?></td>
                <td><?= htmlspecialchars($o['licencia']) ?></td>
                <td><?= htmlspecialchars($o['disponibilidad']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include 'footer.php'; ?>
