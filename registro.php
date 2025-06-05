<?php
session_start();
include('conexion.php'); 

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
   // $nivel = $_POST['nivel'];//Comentado para futuras incorporaciones

    // Validar que las contraseñas coincidan
    if ($contraseña !== $repite_contraseña) {
        echo "Las contraseñas no coinciden.";
        exit();
    }
        // Se encripta la contraseña usando un hash seguro
    $pass = password_hash($contraseña, PASSWORD_DEFAULT);

        // Se convierte el array de gustos a una cadena separada por comas
    $gustos = implode(", ", $gustos_array);
        // Inicializa variables relacionadas con la imagen de perfil
    $foto_nombre = null;
    $directorio_destino = "uploads/";
    // Validación y procesamiento de la imagen de perfil 
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_nombre_original = basename($_FILES['foto_perfil']['name']);
    $extension = pathinfo($foto_nombre_original, PATHINFO_EXTENSION);
    
     // Se genera un nombre único para evitar conflictos de nombre
    $foto_nombre = uniqid('perfil_') . "." . $extension;
    $ruta_final = $directorio_destino . $foto_nombre;

        // Verifica si el directorio existe; si no, lo crea
    if (!file_exists($directorio_destino)) {
        mkdir($directorio_destino, 0755, true);
        // 0755 establece los permisos (lectura/escritura para el propietario, solo lectura/ejecución para los demás).
        // 'true' permite crear carpetas intermedias.

    }

    // Mueve la imagen desde la carpeta temporal a la ubicación final
    move_uploaded_file($foto_tmp, $ruta_final);
}

    // Se prepara la consulta SQL para insertar un nuevo usuario
    $sql = "INSERT INTO Usuario (nombre, apellidos, nombre_usuario, pass, edad, localidad, instrumento, gustos, /*nivel,*/ foto_perfil) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, /*?,*/ ?)";

        // Se prepara la sentencia para evitar inyecciones SQL
    $stmt = $conn->prepare($sql);
        // Se enlazan los parámetros a la consulta
$stmt->bind_param("ssssissss", $nombre, $apellidos, $nombre_usuario, $pass, $edad, $localidad, $instrumento, $gustos, $foto_nombre);//Faltaría nivel

        // Se enlazan los parámetros a la consulta
    if ($stmt->execute()) {
        // Si se inserta correctamente, se guardan datos del usuario en la sesión
        $_SESSION['id_usuario'] = $conn->insert_id; // Obtener  id del nuevo usuario
        $_SESSION['nombre_usuario'] = $nombre_usuario;
        $_SESSION['nombre'] = $nombre;

        // Redirigir a la página principal del usuario
        header("Location: home.php");
        exit();
    } else {
                // En caso de error al ejecutar la consulta
        echo "Error: " . $stmt->error;
    }
        // Se cierra la sentencia y la conexión
    $stmt->close();
    $conn->close();
}
?>
