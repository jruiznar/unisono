<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    echo "No autorizado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['biografia'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $biografia = trim($_POST['biografia']);

    $sql = "UPDATE usuario SET biografia = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $biografia, $id_usuario);

    if ($stmt->execute()) {
        echo "Biografía actualizada correctamente.";
    } else {
        echo "Error al actualizar biografía.";
    }

    $stmt->close();
}
?>
