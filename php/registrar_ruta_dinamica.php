<?php
require 'conexion.php';

// Detectar si es petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function responder($esAjax, $redirectUrl, $status, $mensaje)
{
    if ($esAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'mensaje' => $mensaje]);
        exit;
    }
    header('Location: ' . $redirectUrl);
    exit;
}

if (isset($_POST['nombre_ruta'], $_POST['origen'], $_POST['destino'])) {
    $nombre_ruta = $_POST['nombre_ruta'];
    list($lat_origen, $lon_origen) = explode(',', $_POST['origen']);
    list($lat_destino, $lon_destino) = explode(',', $_POST['destino']);
    $paradas = isset($_POST['paradas']) ? explode('|', $_POST['paradas']) : [];

    $sql = "INSERT INTO rutas (nombre_ruta, lat_origen, lon_origen, lat_destino, lon_destino, fecha_creacion) VALUES (:nombre_ruta, :lat_origen, :lon_origen, :lat_destino, :lon_destino, NOW())";
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute([
        'nombre_ruta' => $nombre_ruta,
        'lat_origen' => $lat_origen,
        'lon_origen' => $lon_origen,
        'lat_destino' => $lat_destino,
        'lon_destino' => $lon_destino
    ]);

    if ($resultado) {
        $id_ruta = $conexion->lastInsertId();

        $orden = 1;
        foreach ($paradas as $parada) {
            list($lat, $lon) = explode(',', $parada);

            $sql_parada = "INSERT INTO paradas (id_ruta, orden, latitud, longitud, descripcion) VALUES (:id_ruta, :orden, :latitud, :longitud, :descripcion)";
            $stmt_parada = $conexion->prepare($sql_parada);
            $stmt_parada->execute([
                'id_ruta' => $id_ruta,
                'orden' => $orden,
                'latitud' => $lat,
                'longitud' => $lon,
                'descripcion' => 'Parada ' . $orden
            ]);

            $orden++;
        }

        responder($esAjax, '../ver_rutas.php?mensaje=registrado', 'ok', 'Ruta registrada correctamente.');
    } else {
        responder($esAjax, '../ver_rutas.php', 'error', 'Error al registrar la ruta.');
    }
} else {
    responder($esAjax, '../ver_rutas.php', 'error', 'Faltan datos obligatorios.');
}
?>