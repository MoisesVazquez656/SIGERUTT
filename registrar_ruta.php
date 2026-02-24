<?php include 'header.php'; ?>

<h2>Registrar Ruta</h2>

<div class="contenedor-flex">

    <!-- Formulario -->
    <div class="formulario-limpio">
        <form action="php/registrar_ruta.php" method="POST">
            <label>Nombre de la Ruta:</label>
            <input type="text" name="nombre_ruta" required>

            <label>Código Postal Origen:</label>
            <input type="text" id="codigo_origen" placeholder="Ingrese Código Postal">
            <button type="button" id="buscar_origen">Buscar Origen</button>

            <label>Código Postal Destino:</label>
            <input type="text" id="codigo_destino" placeholder="Ingrese Código Postal">
            <button type="button" id="buscar_destino">Buscar Destino</button>

            <label>Código Postal Parada (Opcional):</label>
            <input type="text" id="codigo_parada" placeholder="Ingrese Código Postal">
            <button type="button" id="buscar_parada">Buscar Parada</button>

            <label>Distancia Total:</label>
            <input type="text" id="distancia_mostrada" readonly>
            <input type="hidden" id="distancia_total" name="distancia_total">

            <!-- Campos ocultos -->
            <input type="hidden" id="origen" name="origen" required>
            <input type="hidden" id="destino" name="destino" required>
            <input type="hidden" id="paradas" name="paradas">

            <button type="submit">Registrar Ruta</button>
        </form>

        <!-- Instrucciones -->
        <div class="instrucciones">
            <p>📌 Da clic en el mapa para agregar puntos.</p>
            <p>🗑️ Da clic sobre un marcador para eliminarlo.</p>
            <p>🔎 Usa el buscador por código postal para acercarte a una zona.</p>
        </div>
    </div>

    <!-- Mapa -->
    <div id="map" class="mapa"></div>
</div>

<br>
<a href="ver_rutas.php" class="boton-regresar">Regresar al Menú Principal</a>

<!-- Librerías -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<script>
    var map = L.map('map').setView([17.9895, -92.9256], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

    var waypoints = [];
    var markers = [];
    var routingControl;

    // Agregar marcador al dar clic en el mapa
    map.on('click', function (e) {
        agregarPunto(L.latLng(e.latlng.lat, e.latlng.lng));
    });

    // Agregar marcador
    function agregarPunto(latlng) {
        var marker = L.marker(latlng).addTo(map).bindPopup('Haz clic para eliminar').openPopup();

        marker.on('click', function (e) {
            e.originalEvent.preventDefault();
            e.originalEvent.stopPropagation(); // Esta es la clave
            eliminarMarcador(marker);
        });

        markers.push(marker);
        waypoints.push(latlng);
        actualizarRuta();
    }

    // Eliminar marcador
    function eliminarMarcador(marker) {
        var index = markers.indexOf(marker);
        if (index !== -1) {
            map.removeLayer(marker);
            markers.splice(index, 1);
            waypoints.splice(index, 1);
            actualizarRuta();
        }
    }

    // Actualizar ruta y campos
    function actualizarRuta() {
        if (routingControl) {
            map.removeControl(routingControl);
        }

        if (waypoints.length >= 2) {
            routingControl = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: true,
                draggableWaypoints: true,
                addWaypoints: true,
                show: false
            }).addTo(map);

            routingControl.on('routesfound', function (e) {
                actualizarCampos(e.routes[0]);
            });
        } else {
            limpiarCampos();
        }
    }

    function actualizarCampos(ruta) {
        var wp = ruta.waypoints;

        if (wp.length >= 2) {
            var origen = wp[0].latLng.lat + ',' + wp[0].latLng.lng;
            var destino = wp[wp.length - 1].latLng.lat + ',' + wp[wp.length - 1].latLng.lng;

            var paradas = [];
            for (var i = 1; i < wp.length - 1; i++) {
                paradas.push(wp[i].latLng.lat + ',' + wp[i].latLng.lng);
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
    }

    function limpiarCampos() {
        document.getElementById('origen').value = '';
        document.getElementById('destino').value = '';
        document.getElementById('paradas').value = '';
        document.getElementById('distancia_total').value = '';
        document.getElementById('distancia_mostrada').value = '';
    }

    // Buscadores de código postal
    document.getElementById('buscar_origen').addEventListener('click', function () {
        buscarCodigoPostal('codigo_origen');
    });

    document.getElementById('buscar_destino').addEventListener('click', function () {
        buscarCodigoPostal('codigo_destino');
    });

    document.getElementById('buscar_parada').addEventListener('click', function () {
        buscarCodigoPostal('codigo_parada');
    });

    function buscarCodigoPostal(inputId) {
        var codigoPostal = document.getElementById(inputId).value.trim();

        if (codigoPostal === '') {
            alert('Por favor, ingrese un código postal.');
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?postalcode=${codigoPostal}&country=Mexico&format=json`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;
                    var latlng = L.latLng(lat, lon);

                    agregarPunto(latlng);
                    map.setView(latlng, 15);
                } else {
                    alert('Código postal no encontrado. Intenta con otro.');
                }
            })
            .catch(error => {
                console.error('Error al buscar el código postal:', error);
            });
    }
</script>

<?php include 'footer.php'; ?>
