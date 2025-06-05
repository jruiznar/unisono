<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$titulo = $_POST['nombreEvento'] ?? '';
$ubicacion = $_POST['ubicacionEvento'] ?? '';
$fecha = $_POST['fechaEvento'] ?? '';
$hora = $_POST['horaEvento'] ?? '';
$descripcion = $_POST['descripcionEvento'] ?? '';
$id_evento = $_POST['id_evento'] ?? null;

// Manejo de imagen
$nombre_imagen = '';
if (isset($_FILES['imagenEvento']) && $_FILES['imagenEvento']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['imagenEvento']['tmp_name'];
    $nombre_original = basename($_FILES['imagenEvento']['name']);
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    $nombre_imagen = uniqid() . '.' . $extension;

    $ruta_destino = '../imagenes_eventos/' . $nombre_imagen;
    move_uploaded_file($tmp_name, $ruta_destino);
} elseif ($id_evento) {
    // Si no se sube imagen nueva pero estamos editando, conservar la actual!!!
    $stmtImg = $conn->prepare("SELECT imagen FROM evento WHERE id_evento = ?");
    $stmtImg->bind_param("i", $id_evento);
    $stmtImg->execute();
    $stmtImg->bind_result($imagen_existente);
    if ($stmtImg->fetch()) {
        $nombre_imagen = $imagen_existente;
    }
    $stmtImg->close();
}

if ($id_evento) {
    // ACTUALIZAR EVENTO
    $sql = "UPDATE evento 
            SET titulo = ?, ubicacion = ?, fecha = ?, hora = ?, descripcion = ?, imagen = ?
            WHERE id_evento = ? AND id_creador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $titulo, $ubicacion, $fecha, $hora, $descripcion, $nombre_imagen, $id_evento, $id_usuario);
} else {
    // CREAR NUEVO EVENTO
    $sql = "INSERT INTO evento (titulo, ubicacion, fecha, hora, descripcion, imagen, id_creador)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $titulo, $ubicacion, $fecha, $hora, $descripcion, $nombre_imagen, $id_usuario);
}

if ($stmt->execute()) {
    if (!$id_evento) {
        $id_evento = $stmt->insert_id;
    }

    if (isset($_POST['invitados']) && is_array($_POST['invitados'])) {
        $stmtDel = $conn->prepare("DELETE FROM evento_invitado WHERE id_evento = ?");
        $stmtDel->bind_param("i", $id_evento);
        $stmtDel->execute();
        $stmtDel->close();

        $stmtInv = $conn->prepare("INSERT INTO evento_invitado (id_evento, id_usuario) VALUES (?, ?)");
        foreach ($_POST['invitados'] as $invitado_id) {
            $stmtInv->bind_param("ii", $id_evento, $invitado_id);
            $stmtInv->execute();
        }
        $stmtInv->close();
    }

    header("Location: /unisono/home.php");
    exit();
}
 else {
    echo "Error al guardar el evento: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
