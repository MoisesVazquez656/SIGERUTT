<?php
require 'conexion.php';

if (isset($_POST['id_ruta'], $_POST['origen'], $_POST['destino'], $_POST['paradas'], $_POST['distancia_total'])) {
    $id_ruta = $_POST['id_ruta'];
    list($lat_origen, $lon_origen) = explode(',', $_POST['origen']);
    list($lat_destino, $lon_destino) = explode(',', $_POST['destino']);
    $paradas = isset($_POST['paradas']) && !empty($_POST['paradas']) ? explode('|', $_POST['paradas']) : [];
    $distancia_total = $_POST['distancia_total'];

    $sql = "UPDATE rutas SET lat_origen = :lat_origen, lon_origen = :lon_origen, lat_destino = :lat_destino, lon_destino = :lon_destino, distancia_total = :distancia_total WHERE id_ruta = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'lat_origen' => $lat_origen,
        'lon_origen' => $lon_origen,
        'lat_destino' => $lat_destino,
        'lon_destino' => $lon_destino,
        'distancia_total' => $distancia_total,
        'id' => $id_ruta
    ]);

    $sql_delete = "DELETE FROM paradas WHERE id_ruta = :id";
    $stmt_delete = $conexion->prepare($sql_delete);
    $stmt_delete->execute(['id' => $id_ruta]);

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

    header('Location: ../ver_rutas.php?mensaje=actualizado');
    exit();
} else {
    header('Location: ../ver_rutas.php');
    exit();
}
?>
