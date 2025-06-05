<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['comentario']) || !isset($_POST['id_video'])) {
    http_response_code(400);
    echo "Faltan datos.";
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_video = intval($_POST['id_video']);
$comentario = trim($_POST['comentario']);

if (empty($comentario)) {
    http_response_code(400);
    echo "Comentario vacío.";
    exit();
}

$sql = "INSERT INTO comentario (id_video, id_usuario, comentario) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $id_video, $id_usuario, $comentario);

if ($stmt->execute()) {
    header("Location: /unisono/home.php");  
} else {
    http_response_code(500);
    echo "Error al guardar.";
}
