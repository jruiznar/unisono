<?php
session_start();
include('conexion.php');


// Verifica si el usuario está autenticado (tiene sesión iniciada)
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtiene el ID del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];
// Obtiene la fecha pasada por parámetro GET (opcional)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

// Inicializa un array vacío para almacenar los eventos
$eventos = [];

// Rutas de imágenes
$imagenDefecto = '/unisono/iconos/evento.jpg'; // ruta imagen por defecto
$rutaImagenes = '/unisono/uploads/'; // ruta carpeta imágenes

if ($fecha) {
    // Eventos creados por mí en esa fecha (color rojo)
$sql = "SELECT e.id_evento, e.fecha, e.titulo, 'creado' AS tipo, 'rojo' AS color, e.imagen, u.nombre_usuario
        FROM evento e
        INNER JOIN usuario u ON e.id_creador = u.id_usuario
        WHERE e.id_creador = ? AND e.fecha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_usuario, $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    
        // Recorre los resultados y los añade al array $eventos
while ($row = $result->fetch_assoc()) {
        $urlImagen = !empty($row['imagen']) ? $rutaImagenes . $row['imagen'] : $imagenDefecto;
        $eventos[] = [
            'id_evento' => $row['id_evento'],
            'fecha' => $row['fecha'],
            'titulo' => $row['titulo'],
            'tipo' => $row['tipo'],//creado
            'nombre_usuario' => $row['nombre_usuario'],
            'color' => $row['color'],
            'imagen_url' => $urlImagen
        ];
    }
    $stmt->close();

    // Eventos donde estoy invitado en esa fecha (color amarillo)
   $sql = "SELECT e.id_evento, e.fecha, e.titulo, 'invitado' AS tipo, 'amarillo' AS color, e.imagen, u.nombre_usuario
        FROM evento e
        INNER JOIN evento_invitado ei ON ei.id_evento = e.id_evento
        INNER JOIN usuario u ON e.id_creador = u.id_usuario
        WHERE ei.id_usuario = ? AND e.fecha = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_usuario, $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    
        // Añade estos eventos al array $eventos
while ($row = $result->fetch_assoc()) {
        $urlImagen = !empty($row['imagen']) ? $rutaImagenes . $row['imagen'] : $imagenDefecto;
        $eventos[] = [
            'id_evento' => $row['id_evento'],
            'fecha' => $row['fecha'],
            'titulo' => $row['titulo'],
            'tipo' => $row['tipo'],//invitado
            'nombre_usuario' => $row['nombre_usuario'],
            'color' => $row['color'],
            'imagen_url' => $urlImagen
        ];
    }
    $stmt->close();

} else {
    // TODOS LOS EVENTOS CREADOS POR EL USUARIO
$sql = "SELECT e.id_evento, e.fecha, e.titulo, 'creado' AS tipo, 'rojo' AS color, e.imagen, u.nombre_usuario
        FROM evento e
        INNER JOIN usuario u ON e.id_creador = u.id_usuario
        WHERE e.id_creador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $urlImagen = !empty($row['imagen']) ? $rutaImagenes . $row['imagen'] : $imagenDefecto;
        $eventos[] = [
            'id_evento' => $row['id_evento'],
            'fecha' => $row['fecha'],
            'titulo' => $row['titulo'],
            'tipo' => $row['tipo'],
            'nombre_usuario' => $row['nombre_usuario'],
            'color' => $row['color'],
            'imagen_url' => $urlImagen
        ];
    }
    $stmt->close();

    // TODOS LOS EVENTOS DONDE ESTÁ INVITADO EL USUARIO
    $sql = "SELECT e.id_evento, e.fecha, e.titulo, 'invitado' AS tipo, 'amarillo' AS color, e.imagen, u.nombre_usuario
        FROM evento e
        INNER JOIN evento_invitado ei ON ei.id_evento = e.id_evento
        INNER JOIN usuario u ON e.id_creador = u.id_usuario
        WHERE ei.id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $urlImagen = !empty($row['imagen']) ? $rutaImagenes . $row['imagen'] : $imagenDefecto;
        $eventos[] = [
            'id_evento' => $row['id_evento'],
            'fecha' => $row['fecha'],
            'titulo' => $row['titulo'],
            'tipo' => $row['tipo'],
            'nombre_usuario' => $row['nombre_usuario'],
            'color' => $row['color'],
            'imagen_url' => $urlImagen
        ];
    }
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($eventos);
