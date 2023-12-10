<?php
// Datos de conexión a la base de datos
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

// Crear la conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


