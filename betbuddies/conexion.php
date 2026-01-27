<?php
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$baseDeDatos = "Betbuddies";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDeDatos;charset=utf8",
        $usuario,
        $contraseña,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

