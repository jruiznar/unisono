<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('../conexion.php');

if (!isset($_SESSION['usuario_id']) || !isset($_FILES['video'])) {
    echo "Error de sesión o archivo no recibido.";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$descripcion = $_POST['descripcion'] ?? '';
$video = $_FILES['video'];

var_dump($video); 
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
    echo "Archivo movido correctamente a $destino";
$ruta_guardada = 'uploads/' . $nombre_archivo;
    $sql = "INSERT INTO video (id_usuario, descripcion, url_video) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $usuario_id, $descripcion, $ruta_guardada);
    $stmt->execute();
    $stmt->close();

    
} else {
    echo "Error al mover el archivo.";
}
?>
