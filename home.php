<?php
session_start();
include('conexion.php');

// Si no hay sesión iniciada, redirige al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];



// Obtener datos del usuario actual
$sql = "SELECT * FROM Usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


// Contar eventos pendientes para el usuario actual
$sqlEventosPendientes = "
    SELECT COUNT(*) AS total_pendientes
    FROM evento_invitado ei
    JOIN evento e ON ei.id_evento = e.id_evento
    WHERE ei.id_usuario = ?
    AND e.fecha >= CURDATE()
";
$stmtEventos = $conn->prepare($sqlEventosPendientes);
$stmtEventos->bind_param("i", $user_id);
$stmtEventos->execute();
$resultEventos = $stmtEventos->get_result();
$eventosPendientes = 0;
if ($rowEventos = $resultEventos->fetch_assoc()) {
    $eventosPendientes = $rowEventos['total_pendientes'];
}
$stmtEventos->close();



// Obtener edad para lista de sugerencias
$edad_usuario = isset($user['edad']) ? intval($user['edad']) : 0;


/* Consulta para obtener usuarios ordenados:
 1. Misma localidad 
 2. Mismo instrumento 
 3. Diferencia de edad (ascendente, los más cercanos primero)*/
$sqlUsuariosOrdenados = "
    SELECT u.id_usuario, u.nombre, u.apellidos, u.instrumento, u.localidad, 
           u.edad, u.foto_perfil,
           (u.localidad = ?) AS misma_localidad,
           (u.instrumento = ?) AS mismo_instrumento,
           ABS(u.edad - ?) AS diferencia_edad
    FROM Usuario u
    WHERE u.id_usuario != ?
      AND u.id_usuario NOT IN (
          SELECT seguido_id FROM seguimiento WHERE seguidor_id = ?
      )
    ORDER BY
        misma_localidad DESC,
        mismo_instrumento DESC,
        diferencia_edad ASC,
        u.nombre ASC
    LIMIT 20
";

$user_localidad = $user['localidad'];
$user_instrumento = $user['instrumento'];
$user_edad = $edad_usuario;

$stmtUsuariosOrdenados = $conn->prepare($sqlUsuariosOrdenados);
$stmtUsuariosOrdenados->bind_param("isiii", $user_localidad, $user_instrumento, $user_edad, $user_id, $user_id);
$stmtUsuariosOrdenados->execute();
$resultUsuariosOrdenados = $stmtUsuariosOrdenados->get_result();

$stmtUsuariosOrdenados->close();



// OBTENER FOTO Y NOMBRE PROFESIONAL
$sql = "SELECT * FROM Usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$foto = !empty($user['foto_perfil']) ? 'uploads/' . $user['foto_perfil'] : './iconos/perfil.png';


// Diccionario instrumento-profesión
$profesionales = [
    'guitarra' => 'Guitarrista',
    'bajo' => 'Bajista',
    'piano' => 'Pianista',
    'bateria' => 'Baterista'
];

$instrumento = strtolower(trim($user['instrumento']));
$profesional = isset($profesionales[$instrumento]) ? $profesionales[$instrumento] : $user['instrumento'];
?>
<?php
// Especificamos modos de feed para no mostar datos de usuarios cuando visitas perfil
$modoFeed = 'home';
include 'feed.php';

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mi página</title>
    <!-- Hojas de estilos -->
    <link rel="stylesheet" href="/unisono/css/stylesPrincipal.css" />
    <link rel="stylesheet" href="/unisono/css/perfil.css" />
    <link rel="stylesheet" href="/unisono//css/calendario.css" />
    <link rel="stylesheet" href="/unisono/css/busqueda.css"/>
    <link rel="stylesheet" href="/unisono/css/crearEvento.css" />
    <link rel="stylesheet" href="/unisono/css/feed.css">
    <link rel="stylesheet" href="/unisono/css/otroPerfil.css">

    <!-- Script con defer para que no de error antes de cargar todo -->
      <!-- <script src="/unisono/js/busqueda.js" defer></script>
      <script src="/unisono/js/evento.js" defer></script>
      <script src="/unisono/js/modalInvitados.js" defer></script> -->
</head>

<body>
<div class="contenedor-general">
 <!-- HEADER ESTÁTICO -->
    <div class="superior-container">
        <div class="logo">
            <a href="home.php">
            <img src="./iconos/unisono.png" id="logo" alt="Logo" />
            </a>
        </div>
        
        <div class="busqueda">
            <button class="boton-lupa">
                <img src="./iconos/lupa.png" alt="Buscar">
            </button>
            <input type="text" id="cuadro-busqueda" placeholder="Buscar...">
        </div>

        <div class="botones-derecha">
            <button class="btn-del" id="btn-del">
                <img src="./iconos/del.png" alt="Eliminar usuario" />
            </button>
            <button class="btn-exit" id="btn-exit">
                <img src="./iconos/exit.png" alt="Salir" />
            </button>
        </div>
    </div>

    <!--  IZQUIERDO ESTÁTICO -->
    <div class="izquierda-container">
        <!-- Mi Perfil -->
        <div class="mi-perfil">
            <div class="foto-container">
                <img id="foto-perfil" class="fotoPerfil" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" style="cursor:pointer;" />
                <label class="instrumento"><?php echo htmlspecialchars($profesional); ?></label>
            </div>
            <div class="info-perfil">
                <label class="nombre"><?php echo htmlspecialchars($user['nombre']); ?></label>
                <label class="localidad"><?php echo htmlspecialchars($user['localidad']); ?></label>
            </div>
        </div>
      <!-- Eventos -->
        <div class="eventos">
            <div class="evento-invitaciones">
                <!-- Indicamos los eventos pendientes que tiene el usuario, haciendolo plural si tiene mas de 1 -->
                Tienes <?= $eventosPendientes ?> Evento<?= $eventosPendientes !== 1 ? 's' : '' ?> pendiente<?= $eventosPendientes !== 1 ? 's' : '' ?>
            </div>
            <div class="evento-boton">
                <button id="crear-evento-btn" type="button">Crear evento</button>
            </div>    
        </div>

        <!-- Calendario -->
        <div class="calendario">
            <div class="calendario-header">
                <button id="prev-month">&lt;</button>
                <!-- Indicamos luego newDato en JS -->
                <h2 id="mes-actual">Marzo 2025</h2>
                <button id="next-month">&gt;</button>
            </div>
            <div class="calendario-dias-semana">
                <div>L</div><div>M</div><div>X</div><div>J</div><div>V</div><div>S</div><div>D</div>
            </div>
            <div class="calendario-dias" id="calendario-dias"></div>
        </div>
    </div>

    <!--  CENTRAL DINÁMICO -->
    <div class="central-container" id="central-container">
            <!-- Indicamos el que sería el div central principal, que cambiara dinámicamente -->
            <?php include("central/feed.php"); ?>
    </div>

<!--  DERECHO ESTÁTICO -->
<div class="derecha-container">
    <!-- Botón deshabilitado, listo para incorporaciones futuras -->
    <!-- <button id="btnChat" class="btn-chat">Chat</button> -->
    <div class="lista-usuarios">
        <h3>Sugerencias</h3>
        <?php
        $max_visibles_usuarios = 4;
        $contador = 0;
        ?>
        <ul>
        <?php while ($row = $resultUsuariosOrdenados->fetch_assoc()): ?>
            <!-- Si supera 4 añade la clase oculta para boton -->
            <li class="usuario-sugerido <?= ($contador >= $max_visibles_usuarios) ? 'oculta' : '' ?>">
                <?php
                    $nombre = htmlspecialchars($row['nombre'] ?? '');
                    $apellidos = htmlspecialchars($row['apellidos'] ?? '');
                    $edad = isset($row['edad']) ? intval($row['edad']) : '?';
                    $instrumento = !empty($row['instrumento']) ? ucfirst(htmlspecialchars($row['instrumento'])) : 'No disponible';
                    $localidad = htmlspecialchars($row['localidad'] ?? 'No disponible');
                    /*Muestra foto sino la foto de perfil por defecto, podríamos subir la foto a la bbdd 
                    para no tener que reflejarla por defecto en cada mencion al perfil, pero saturaríamos la bbbdd */   
                    $fotoUsuario = !empty($row['foto_perfil']) ? 'uploads/' . $row['foto_perfil'] : './iconos/perfil.png';
                ?>
                
                <a href="#" class="ver-perfil" data-id="<?= $row['id_usuario'] ?>">
                    <img src="<?= $fotoUsuario ?>" alt="Foto de <?= $nombre ?>" class="foto-mini">
                    <div class="info-usuario">
                        <!-- Uso de strong y small para evitar añadir más clases en la hoja de estilos -->
                        <strong><?= $nombre . ' ' . $apellidos ?></strong><br>
                        <small>
                            Localidad: <?= $localidad ?><br>
                            Instrumento: <?= $instrumento ?><br>
                            Edad: <?= $edad ?> años
                        </small>
                    </div>
                </a>
            </li>
        <?php
        $contador++;
        endwhile;
        ?>
        </ul>
<!-- Comprobamos contador para hacer aparecer el botón ver mas -->
        <?php if ($contador > $max_visibles_usuarios): ?>
            <button id="ver-mas-usuarios" class="btn-ver-mas">Ver más</button>
        <?php endif; ?>
    </div>
</div>


</div> 
<!-- Modal para eventos pendientes -->
<div id="modal-eventos" class="modal oculto">
  <div class="modal-contenido">
    <span class="cerrar" onclick="cerrarModalEventos()">&times;</span>
    <h3 id="titulo-modal-eventos">Eventos pendientes</h3>
    <div id="contenido-modal-eventos">
      <!-- Eventos añadidos mediante JS -->
    </div>
  </div>
</div>


<script src="js/perfil.js"></script>
<script>
    //introduce miperfil en div central
  document.getElementById('foto-perfil').addEventListener('click', function() {
    fetch('central/miPerfil.php')
      .then(response => response.text())
      .then(html => {
        document.getElementById('central-container').innerHTML = html;
        if (typeof initPerfilJS === 'function') {
            initPerfilJS();
        }
      })
      .catch(error => {
        console.error('Error al cargar perfil:', error);
      });
  });
</script>
<!--<script src="./js/busqueda.js"></script>-->
<script src="js/calendario.js"></script>
<script src="js/seguir.js"></script>
<script src="js/tecnicas.js"></script>
<script src="js/feed.js"></script>
<script src="js/modalInvitados.js"></script>

<script src="/unisono/js/crearEvento.js" defer></script>
<script src="/unisono/js/verMasUsuarios.js" defer></script>
<script src="/unisono/js/salirSesion.js"></script>
<script src="/unisono/js/eliminarUsuario.js" defer></script>
<script src="/unisono/js/busqueda.js" defer></script>
      <script src="/unisono/js/evento.js" defer></script>
      <script src="/unisono/js/modalInvitados.js" defer></script>
</body>
</html>
