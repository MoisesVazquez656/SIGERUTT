<?php
require 'php/conexion.php';
include 'header.php';

$origen = isset($_GET['origen']) ? $_GET['origen'] : 'ver_rutas.php';

if (!isset($_GET['id'])) {
    header('Location: ver_rutas.php');
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM rutas WHERE id_ruta = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id' => $id]);
$ruta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ruta) {
    header('Location: ver_rutas.php');
    exit();
}

$sql_paradas = "SELECT * FROM paradas WHERE id_ruta = :id ORDER BY orden ASC";
$stmt_paradas = $conexion->prepare($sql_paradas);
$stmt_paradas->execute(['id' => $id]);
$paradas = $stmt_paradas->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Visualización de Ruta: <?php echo htmlspecialchars($ruta['nombre_ruta']); ?></h2>

<div id="map" style="height: 500px; margin-bottom: 20px;"></div>

<a href="javascript:window.history.back();" class="boton-regresar">Regresar al Menú Anterior</a>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<?php
$origen = isset($_GET['origen']) ? $_GET['origen'] : 'rutas';

if ($origen == 'asignaciones') {
    $url_regresar = 'ver_asignaciones.php';
} else {
    $url_regresar = 'ver_rutas.php';
}
?>

<script>
    var map = L.map('map').setView([<?php echo $ruta['lat_origen']; ?>, <?php echo $ruta['lon_origen']; ?>], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

    // Definir los puntos de la ruta
    var waypoints = [
        L.latLng(<?php echo $ruta['lat_origen']; ?>, <?php echo $ruta['lon_origen']; ?>),
        <?php foreach ($paradas as $parada): ?>
            L.latLng(<?php echo $parada['latitud']; ?>, <?php echo $parada['longitud']; ?>),
        <?php endforeach; ?>
        L.latLng(<?php echo $ruta['lat_destino']; ?>, <?php echo $ruta['lon_destino']; ?>)
    ];

    // Mostrar la ruta sin permitir edición
    L.Routing.control({
        waypoints: waypoints,
        routeWhileDragging: false,
        draggableWaypoints: false,
        addWaypoints: false,
        createMarker: function(i, wp, nWps) {
            return L.marker(wp.latLng).bindPopup(i === 0 ? 'Origen' : (i === nWps - 1 ? 'Destino' : 'Parada ' + i));
        }
    }).addTo(map);
</script>

<?php include 'footer.php'; ?>
