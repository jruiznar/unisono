<?php
session_start();
include('../conexion.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if (!isset($_FILES['foto_perfil'])) {
    echo json_encode(['success' => false, 'error' => 'No se ha enviado ningún archivo']);
    exit();
}

$archivo = $_FILES['foto_perfil'];

// Validar tipo y tamaño de archivo
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($archivo['type'], $tiposPermitidos)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido']);
    exit();
}

if ($archivo['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'Archivo demasiado grande (máximo 2MB)']);
    exit();
}

// Crear carpeta uploads si no existe
$carpetaUploads = '../uploads/';
if (!is_dir($carpetaUploads)) {
    mkdir($carpetaUploads, 0755, true);
}

// Genera nombre único para el archivo
$ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
$nombreArchivo = 'perfil_' . $id_usuario . '_' . time() . '.' . $ext;
$rutaDestino = $carpetaUploads . $nombreArchivo;

if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    // Actualizar base de datos
    $sql = "UPDATE usuario SET foto_perfil = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombreArchivo, $id_usuario);
    $stmt->execute();
    $stmt->close();

     
    $rutaWeb = '/unisono/uploads/' . $nombreArchivo;

    echo json_encode(['success' => true, 'nuevaRuta' => $rutaWeb]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al mover el archivo']);
}
