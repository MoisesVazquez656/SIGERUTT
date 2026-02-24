<?php
require 'php/conexion.php';
include 'header.php';

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

<h2>Editar Ruta: <?php echo htmlspecialchars($ruta['nombre_ruta']); ?></h2>

<div id="map" style="height: 500px; margin-bottom: 20px;"></div>

<form action="php/actualizar_ruta_dinamica.php" method="POST">
    <input type="hidden" name="id_ruta" value="<?php echo $ruta['id_ruta']; ?>">
    <input type="hidden" id="origen" name="origen" required>
    <input type="hidden" id="destino" name="destino" required>
    <input type="hidden" id="paradas" name="paradas">
    <input type="hidden" id="distancia_total" name="distancia_total">

    <label>Distancia Total:</label>
    <input type="text" id="distancia_mostrada" readonly>

    <button type="submit">Guardar Cambios</button>
</form>

<a href="javascript:window.history.back();" class="boton-regresar">Regresar al Menú Anterior</a>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<script>
    var map = L.map('map').setView([<?php echo $ruta['lat_origen']; ?>, <?php echo $ruta['lon_origen']; ?>], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

    var control = L.Routing.control({
        waypoints: [
            L.latLng(<?php echo $ruta['lat_origen']; ?>, <?php echo $ruta['lon_origen']; ?>),
            <?php foreach ($paradas as $parada): ?>
                L.latLng(<?php echo $parada['latitud']; ?>, <?php echo $parada['longitud']; ?>),
            <?php endforeach; ?>
            L.latLng(<?php echo $ruta['lat_destino']; ?>, <?php echo $ruta['lon_destino']; ?>)
        ],
        routeWhileDragging: true
    }).addTo(map);

    function actualizarCampos(ruta) {
        var waypoints = ruta.waypoints;
        var origen = waypoints[0].latLng.lat + ',' + waypoints[0].latLng.lng;
        var destino = waypoints[waypoints.length - 1].latLng.lat + ',' + waypoints[waypoints.length - 1].latLng.lng;
        var paradas = [];

        for (var i = 1; i < waypoints.length - 1; i++) {
            paradas.push(waypoints[i].latLng.lat + ',' + waypoints[i].latLng.lng);
        }

        document.getElementById('origen').value = origen;
        document.getElementById('destino').value = destino;
        document.getElementById('paradas').value = paradas.join('|');

        var distancia = 0;
        ruta.coordinates.forEach(function (coord, index, arr) {
            if (index < arr.length - 1) {
                var latlng1 = L.latLng(coord.lat, coord.lng);
                var latlng2 = L.latLng(arr[index + 1].lat, arr[index + 1].lng);
                distancia += latlng1.distanceTo(latlng2);
            }
        });

        var km = (distancia / 1000).toFixed(2);
        document.getElementById('distancia_total').value = km;
        document.getElementById('distancia_mostrada').value = km + ' km';
    }

    control.on('routesfound', function (e) {
        actualizarCampos(e.routes[0]);
    });
</script>

<?php include 'footer.php'; ?>
