  <?php 

// ?> 
<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "unisono";
$port = 3306; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
