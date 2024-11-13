<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ferreteria_clavijo"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
