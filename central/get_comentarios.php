<?php
include('../conexion.php');

$id_video = intval($_GET['id_video'] ?? 0);

$sql = "SELECT c.comentario, u.nombre, u.foto_perfil
        FROM comentario c
        JOIN usuario u ON c.id_usuario = u.id_usuario
        WHERE c.id_video = ?
        ORDER BY c.fecha_comentario DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_video);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = [
        'comentario' => $row['comentario'],
        'nombre' => $row['nombre'],
        'foto_perfil' => $row['foto_perfil'] ?? 'perfil.png'
    ];
}


header('Content-Type: application/json');
echo json_encode($comentarios);
