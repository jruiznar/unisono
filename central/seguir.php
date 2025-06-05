<?php
session_start();
include('../conexion.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['seguido_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Parámetros faltantes']);
    exit();
}

$seguidor_id = $_SESSION['id_usuario'];
$seguido_id = intval($_POST['seguido_id']);

if ($seguidor_id === $seguido_id) {
    echo json_encode(['status' => 'error', 'message' => 'No puedes seguirte a ti mismo']);
    exit();
}

// Verificar si ya existe el seguimiento
$sql_check = "SELECT * FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $seguidor_id, $seguido_id);
$stmt_check->execute();
$result = $stmt_check->get_result();
$stmt_check->close();

if ($result->num_rows === 0) {
    // Insertar seguimiento
    $sql_insert = "INSERT INTO seguimiento (seguidor_id, seguido_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $seguidor_id, $seguido_id);
    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'seguido']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al seguir']);
    }
    $stmt_insert->close();
} else {
    // Eliminar seguimiento (dejar de seguir)
    $sql_delete = "DELETE FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $seguidor_id, $seguido_id);
    if ($stmt_delete->execute()) {
        echo json_encode(['status' => 'no_seguido']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al dejar de seguir']);
    }
    $stmt_delete->close();
}

$conn->close();
exit();
