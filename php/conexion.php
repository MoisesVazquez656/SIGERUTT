<?php
$host = "localhost";
$db = "sistema_rutas";
$user = "root"; // Cambia si tu usuario es diferente
$pass = "1234";     // Cambia si tienes contraseña

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
