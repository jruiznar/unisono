<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_evento = isset($_POST['id_evento']) ? intval($_POST['id_evento']) : 0;
    $id_usuario = $_SESSION['id_usuario'];

    if ($id_evento > 0) {
        // Borrar las invitaciones relacionadas con el evento
        $stmt = $conn->prepare("DELETE FROM evento_invitado WHERE id_evento = ?");
        if (!$stmt) {
            die("Error en la consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $id_evento);
        $stmt->execute();
        $stmt->close();

        // Borrar el evento sólo si el usuario es el creador
        $stmt = $conn->prepare("DELETE FROM evento WHERE id_evento = ? AND id_creador = ?");
        if (!$stmt) {
            die("Error en la consulta: " . $conn->error);
        }
        $stmt->bind_param("ii", $id_evento, $id_usuario);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            header("Location: /unisono/home.php");
            exit();
        } else {
            $stmt->close();
            header("Location: /unisono/central/calendario.php?error=No tienes permiso para borrar este evento o no existe");
            exit();
        }
    } else {
        header("Location: /unisono/central/calendario.php?error=ID de evento inválido");
        exit();
    }
} else {
    header("Location: /unisono/central/calendario.php");
    exit();
}
