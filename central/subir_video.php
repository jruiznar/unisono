<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario']) || !isset($_FILES['video'])) {
    echo "Error de sesión o archivo no recibido.";
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$descripcion = $_POST['descripcion'] ?? '';
$video = $_FILES['video'];

// var_dump($video); 
if ($video['error'] !== UPLOAD_ERR_OK) {
    echo "Error en la subida: " . $video['error'];
    exit();
}

$nombre_archivo = time() . '_' . basename($video['name']);
$uploads_dir = __DIR__ . '/../uploads/';

if (!is_dir($uploads_dir)) {
    echo "La carpeta uploads no existe.";
    exit();
}

$destino = $uploads_dir . $nombre_archivo;

if (move_uploaded_file($video['tmp_name'], $destino)) {
    header("Location: /unisono/home.php");  
$ruta_guardada = 'uploads/' . $nombre_archivo;
    $sql = "INSERT INTO video (id_usuario, descripcion, url_video) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id_usuario, $descripcion, $ruta_guardada);
    $stmt->execute();
    $stmt->close();

    
} else {
    echo "Error al mover el archivo.";
}
?>
