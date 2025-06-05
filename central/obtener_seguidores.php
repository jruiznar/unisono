<?php
session_start();
include('../conexion.php');

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT u.id_usuario, u.nombre_usuario, u.foto_perfil 
        FROM usuario u 
        JOIN seguimiento s ON s.seguidor_id = u.id_usuario
        WHERE s.seguido_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $row['foto_perfil'] = $row['foto_perfil'] 
        ? "/unisono/uploads/" . $row['foto_perfil'] 
        : "/unisono/iconos/perfil.png";  
    $usuarios[] = $row;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
