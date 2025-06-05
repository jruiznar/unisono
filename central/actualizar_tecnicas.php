<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nuevaTecnica = trim($_POST['tecnica'] ?? '');

if ($nuevaTecnica === '') {
    echo json_encode(['success' => false, 'error' => 'Técnica vacía']);
    exit();
}

// Obtener técnicas actuales
$sql = "SELECT tecnicas FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$tecnicas = [];
if (!empty($row['tecnicas'])) {
    $tecnicas = json_decode($row['tecnicas'], true);
    if (!is_array($tecnicas)) $tecnicas = [];
}

// Añadir la nueva técnica si no existe
if (!in_array($nuevaTecnica, $tecnicas)) {
    $tecnicas[] = $nuevaTecnica;
}

$tecnicas_json = json_encode($tecnicas);

$sql_update = "UPDATE usuario SET tecnicas = ? WHERE id_usuario = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $tecnicas_json, $id_usuario);
$success = $stmt_update->execute();
$stmt_update->close();

echo json_encode(['success' => $success]);
?>
