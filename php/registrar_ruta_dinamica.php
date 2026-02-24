<?php
require 'conexion.php';

if (isset($_POST['nombre_ruta'], $_POST['origen'], $_POST['destino'])) {
    $nombre_ruta = $_POST['nombre_ruta'];
    list($lat_origen, $lon_origen) = explode(',', $_POST['origen']);
    list($lat_destino, $lon_destino) = explode(',', $_POST['destino']);
    $paradas = isset($_POST['paradas']) ? explode('|', $_POST['paradas']) : [];

    $sql = "INSERT INTO rutas (nombre_ruta, lat_origen, lon_origen, lat_destino, lon_destino, fecha_creacion) VALUES (:nombre_ruta, :lat_origen, :lon_origen, :lat_destino, :lon_destino, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'nombre_ruta' => $nombre_ruta,
        'lat_origen' => $lat_origen,
        'lon_origen' => $lon_origen,
        'lat_destino' => $lat_destino,
        'lon_destino' => $lon_destino
    ]);

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

    header('Location: ../ver_rutas.php?mensaje=registrado');
    exit();
} else {
    header('Location: ../ver_rutas.php');
    exit();
}
?>
