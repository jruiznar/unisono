<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM Usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$foto = !empty($user['foto_perfil']) ? 'uploads/' . $user['foto_perfil'] : './iconos/perfil.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mi página</title>
    <link rel="stylesheet" href="./css/stylesPrincipal.css" />
    <link rel="stylesheet" href="css/stylePerfil.css" />
</head>
<body>

<div class="contenedor-general">

    <!-- HEADER FIJO -->
    <div class="superior-container">
        <div class="logo">
            <img src="./iconos/unisono.png" id="logo" alt="Logo" />
        </div>
        
        <div class="busqueda">
            <button class="boton-lupa">
            <img src="./iconos/lupa.png" alt="Buscar">
            </button>
            <input type="text" id="cuadro-busqueda" placeholder="Buscar...">
        </div>
      <div class="botones-derecha">
            <button class="btn-config">
                <img src="./iconos/config.png" alt="Configuración" />    
            </button>
            <button class="btn-exit">
                <img src="./iconos/exit.png" alt="Salir" />
            </button>
        </div>
    </div>

    <!--  IZQUIERDO FIJO -->
    <div class="izquierda-container">

        <!-- Mi Perfil -->
        <div class="mi-perfil">
            <div class="foto-container">
                <img id="foto-perfil" class="fotoPerfil" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" style="cursor:pointer;" />
                <label class="instrumento"><?php echo htmlspecialchars($user['instrumento']); ?></label>
            </div>
            <div class="info-perfil">
                <label class="nombre"><?php echo htmlspecialchars($user['nombre']); ?></label>
                <label class="localidad"><?php echo htmlspecialchars($user['localidad']); ?></label>
            </div>
        </div>

      <!-- Eventos -->
        <div class="eventos">
            <div class="evento-invitaciones">
                    Tienes 0 Invitaciones a eventos
                </div>
                <div class="evento-boton">
                    <button id="crear-evento-btn" type="button">Crear evento</button>
                </div>
                
        </div>

        <!-- Calendario -->
        <div class="calendario">
  <div class="calendario-header">
    <button id="prev-month">&lt;</button>
    <h2 id="mes-actual">Marzo 2025</h2>
    <button id="next-month">&gt;</button>
  </div>
  <div class="calendario-dias-semana">
    <div>L</div><div>M</div><div>X</div><div>J</div><div>V</div><div>S</div><div>D</div>
  </div>
  <div class="calendario-dias" id="calendario-dias"></div>
</div>
    </div>

    <!--  CENTRAL FLEXIBLE -->
    <div class="central-container" id="central-container">
    </div>

    <!--  DERECHO FIJO -->
    <div class="derecha-container">
        <div class="chat">
        </div>
    </div>

</div> 

<script src="js/perfil.js"></script>

<script>
  document.getElementById('foto-perfil').addEventListener('click', function() {
    //llama el div central de mi perfil
    fetch('central/miPerfil.php')
      .then(response => response.text())
      .then(html => {
        //introducemi perfil en central container
        document.getElementById('central-container').innerHTML = html;
        // carga la funcion de perfil.js
        if (typeof initPerfilJS === 'function') {
            initPerfilJS();
        }
      })
      .catch(error => {
        console.error('Error al cargar perfil:', error);
      });
  });
  //falta poner este script en externo
</script>

<script src="js/calendario.js"></script>

</body>
</html>
