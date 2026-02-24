<?php include 'header.php'; ?>

<h2>Registrar Ruta con Mapa Interactivo</h2>

<div id="map" style="height: 500px; margin-bottom: 20px;"></div>

<form action="php/registrar_ruta_dinamica.php" method="POST">
    <label>Nombre de la Ruta:</label>
    <input type="text" name="nombre_ruta" required>

    <label>Origen:</label>
    <input type="text" id="origen" name="origen" readonly required>

    <label>Destino:</label>
    <input type="text" id="destino" name="destino" readonly required>

    <label>Paradas (coordenadas separadas por "|"):</label>
    <textarea id="paradas" name="paradas" readonly></textarea>

    <button type="submit">Registrar Ruta</button>
</form>

<a href="ver_rutas.php" class="boton-regresar">Regresar al Menú Principal</a>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([17.9895, -92.9256], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

    var origenMarker = null;
    var destinoMarker = null;
    var paradas = [];

    map.on('click', function (e) {
        if (!origenMarker) {
            origenMarker = L.marker(e.latlng).addTo(map).bindPopup('Origen').openPopup();
            document.getElementById('origen').value = e.latlng.lat + ',' + e.latlng.lng;
        } else if (!destinoMarker) {
            destinoMarker = L.marker(e.latlng).addTo(map).bindPopup('Destino').openPopup();
            document.getElementById('destino').value = e.latlng.lat + ',' + e.latlng.lng;
        } else {
            var parada = L.marker(e.latlng).addTo(map).bindPopup('Parada ' + (paradas.length + 1)).openPopup();
            paradas.push(e.latlng.lat + ',' + e.latlng.lng);
            document.getElementById('paradas').value = paradas.join('|');
        }
    });
</script>

<?php include 'footer.php'; ?>
