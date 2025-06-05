<?php
session_start();
include('conexion.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que los campos estén vacíos
    if (!empty($_POST['nombre_usuario']) && !empty($_POST['pass'])) {
        $nombre_usuario = $_POST['nombre_usuario'];
        $pass = $_POST['pass'];

        // Consulta preparada para evitar inyecciones SQL
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        // Validar si existe el usuario
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar contraseña
            if (password_verify($pass, $user['pass'])) {
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['nombre'] = $user['nombre'];

                // Redirigir al home
                header("Location: home.php");
                exit();
            } else {
                // Contraseña incorrecta
                header("Location: index.php?error=pass");
                exit();
            }
        } else {
            // Usuario no existe
            header("Location: index.php?error=user");
            exit();
        }

        $stmt->close();
    } 
    
} else {
    // Si se intenta acceder directamente sin POST
    header("Location: index.php");
    exit();
}
?>
