<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['id_usuario'];

$conn->begin_transaction();

try {
    // Borrar comentarios
    $conn->query("DELETE FROM comentario WHERE id_usuario = $user_id");
    
    // Borrar invitados a eventos donde está 
    $conn->query("DELETE FROM evento_invitado WHERE id_usuario = $user_id");
    
    // Borrar videos
    $conn->query("DELETE FROM video WHERE id_usuario = $user_id");
    
    // Borrar seguimientos donde es seguidor o seguido
    $conn->query("DELETE FROM seguimiento WHERE seguidor_id = $user_id OR seguido_id = $user_id");
    
    // Borrar eventos creados por el usuario
    $conn->query("DELETE FROM evento WHERE id_creador = $user_id");
    
    // BORRAR USUARIO
    $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    //CONFIRMA CAMBIOS
    $conn->commit();

    // Destruir sesión
    session_unset();
    session_destroy();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Si hay algún error revertimos todo los cambios para no dejar cambios sin asociar en la bbdd
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar usuario']);
}
?>
