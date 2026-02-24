<?php
require 'conexion.php';

if (isset($_POST['nombre'])) {
    $nombre = strtolower(trim($_POST['nombre']));
    $sql = "SELECT * FROM usuarios WHERE LOWER(nombre) = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nombre]);

    if ($stmt->fetch()) {
        echo 'duplicado';
    } else {
        echo 'disponible';
    }
}

if (isset($_POST['correo'])) {
    $correo = strtolower(trim($_POST['correo']));
    $sql = "SELECT * FROM usuarios WHERE LOWER(correo) = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$correo]);

    if ($stmt->fetch()) {
        echo 'duplicado';
    } else {
        echo 'disponible';
    }
}
?>
