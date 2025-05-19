<?php
session_start();
include('conexion.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];
    $repite_contraseña = $_POST['repite_contraseña'];
    $edad = $_POST['edad'];
    $localidad = $_POST['localidad'];
    $instrumento = $_POST['instrumento'];
    $gustos_array = isset($_POST['gustos']) ? $_POST['gustos'] : [];
    $nivel = $_POST['nivel'];

    // Validar que las contraseñas coincidan
    if ($contraseña !== $repite_contraseña) {
        echo "Las contraseñas no coinciden.";//Poner debajo de boton de entrar
        exit();
    }

    $pass = password_hash($contraseña, PASSWORD_DEFAULT);
    $gustos = implode(", ", $gustos_array);
    $foto_nombre = null;
    $directorio_destino = "uploads/";
//Comprueba subida fotos sin errores
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_nombre_original = basename($_FILES['foto_perfil']['name']);
    $extension = pathinfo($foto_nombre_original, PATHINFO_EXTENSION);
    
    // Crea un nombre único
    $foto_nombre = uniqid('perfil_') . "." . $extension;
    $ruta_final = $directorio_destino . $foto_nombre;

    // Asegúrate de que la carpeta uploads existe, por si movemos directorio por error
    if (!file_exists($directorio_destino)) {
        mkdir($directorio_destino, 0755, true);
    }

    // Mover la imagen subida
    move_uploaded_file($foto_tmp, $ruta_final);
}

    // Insertar nuevo usuario en la base de datos
    $sql = "INSERT INTO Usuario (nombre, apellidos, nombre_usuario, pass, edad, localidad, instrumento, gustos, nivel, foto_perfil) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisssss", $nombre, $apellidos, $nombre_usuario, $pass, $edad, $localidad, $instrumento, $gustos, $nivel, $foto_nombre);


    if ($stmt->execute()) {
        // Guardar los datos del nuevo usuario en la sesión
        $_SESSION['usuario_id'] = $conn->insert_id; // Obtener  id del nuevo usuario
        $_SESSION['nombre_usuario'] = $nombre_usuario;
        $_SESSION['nombre'] = $nombre;

        // Redirigir a la página principal del usuario
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
