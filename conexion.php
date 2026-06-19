<?php
// Ejemplo de enlace a un SGBD con dominio:
// $host = 'mi-dominio.com';

$host = 'localhost';
$username = 'victor';
$password = 'VictorCM7597!';
$database = 'medallero';

$conexion = new mysqli($host, $username, $password, $database);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>