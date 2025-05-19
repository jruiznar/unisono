<?php
session_start();
include('conexion.php'); // Asegúrate de tener bien la conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que se envían los campos esperados
    if (!empty($_POST['nombre_usuario']) && !empty($_POST['pass'])) {
        $nombre_usuario = $_POST['nombre_usuario'];
        $pass = $_POST['pass'];

        // Consulta segura usando prepared statements
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        // Validar si existe el usuario
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar contraseña
            if (password_verify($pass, $user['pass'])) {
                $_SESSION['usuario_id'] = $user['id_usuario'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['nombre'] = $user['nombre'];

                // Redirigir al home
                header("Location: home.php");
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "El usuario no existe.";
        }

        $stmt->close();
    } else {
        echo "Los campos nombre de usuario y contraseña son obligatorios.";
    }
} else {
    // Si se intenta acceder directamente sin POST
    header("Location: index.html");
    exit();
}
?>
