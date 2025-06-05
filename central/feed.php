<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . '/../conexion.php');

$id_usuario_sesion = $_SESSION['id_usuario'] ?? null;

if (!isset($modoFeed)) {
    $modoFeed = 'home'; // Por defecto modo home
}

switch ($modoFeed) {
    case 'miPerfil':
        // Solo videos propios
        $sql = "SELECT v.*, u.nombre, u.apellidos, u.foto_perfil
                FROM video v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                WHERE v.id_usuario = ?
                ORDER BY v.fecha_subida DESC";
        $parametros = [$id_usuario_sesion];
        $tipos_param = "i";
        $ocultarDatosUsuario = true;
        break;

    case 'otroPerfil':
        if (!isset($perfilIDParaFeed)) {
            echo "Error: falta perfilIDParaFeed";
            exit();
        }
        $sql = "SELECT v.*, u.nombre, u.apellidos, u.foto_perfil
                FROM video v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                WHERE v.id_usuario = ?
                ORDER BY v.fecha_subida DESC";
        $parametros = [$perfilIDParaFeed];
        $tipos_param = "i";
        $ocultarDatosUsuario = true;
        break;

    case 'home':
    default:
        // Videos propios + de usuarios que sigo
        $sql = "SELECT v.*, u.nombre, u.apellidos, u.foto_perfil
                FROM video v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                LEFT JOIN seguimiento s ON s.seguido_id = v.id_usuario AND s.seguidor_id = ?
                WHERE s.seguidor_id = ? OR v.id_usuario = ?
                ORDER BY v.fecha_subida DESC";
        $parametros = [$id_usuario_sesion, $id_usuario_sesion, $id_usuario_sesion];
        $tipos_param = "iii";
        $ocultarDatosUsuario = false;
        break;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos_param, ...$parametros);
$stmt->execute();
$result = $stmt->get_result();
?>



<?php
if (!isset($desdePerfilPropio)) {
    $desdePerfilPropio = false; // Por defecto false si no está definida
}
?>


<?php
if (!isset($desdePerfilPropio)) {
    $desdePerfilPropio = false; // Por defecto false
}
?>



<div class="feed">
    <?php while ($video = $result->fetch_assoc()): ?>
<div class="post" id="video-<?php echo $video['id_video']; ?>">
            <div class="usuario-info">
    <?php if (!$ocultarDatosUsuario): ?>
        <a href="#" class="ver-perfil" data-id="<?php echo $video['id_usuario']; ?>">
            <img class="foto-feed" src="/unisono/uploads/<?php echo htmlspecialchars($video['foto_perfil'] ?? 'perfil.png'); ?>" alt="Foto de perfil">
        </a>
        <a href="#" class="ver-perfil" data-id="<?php echo $video['id_usuario']; ?>">
            <span class="nombre-usuario"><?php echo htmlspecialchars($video['nombre']) . ' ' . htmlspecialchars($video['apellidos']); ?></span>
        </a>
    <?php endif; ?>
</div>
            <div class="video-contenido">
                <video src="/unisono/<?php echo htmlspecialchars($video['url_video']); ?>" controls controlsList="nodownload"></video>
                <?php if (!empty($video['descripcion'])): ?>
                    <p class="descripcion-video"><?php echo nl2br(htmlspecialchars($video['descripcion'])); ?></p>
                <?php endif; ?>
            </div>
<!-- Modal comentarios -->
<div id="modal-comentarios" class="modal oculto">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalComentarios()">&times;</span>
        <h3>Comentarios</h3>
        <ul id="lista-comentarios">
            <!-- JS los carga -->
        </ul>
    </div>
</div>
            <div class="acciones">
    <div class="comentario-header">
<button class="btn-comentarios" data-id="<?php echo $video['id_video']; ?>">
    <img src="/unisono/iconos/coment.png" alt="Ver comentarios" class="icono-comentario">
</button>
        <form class="form-comentario" method="POST" action="/unisono/central/comentar.php">
            <input type="hidden" name="id_video" value="<?php echo $video['id_video']; ?>">
            <input type="text" name="comentario" placeholder="Añade un comentario..." required>
<button type="submit" class="btn-enviar">
    <img src="/unisono/iconos/send.png" alt="Enviar" class="icono-enviar">
</button>
        </form>
    </div>
<div class="comentarios-container" id="comentarios-<?php echo $video['id_video']; ?>"></div>
</div>

        </div>
    <?php endwhile; ?>
    
</div>


        </ul>
    </div>
</div> 

<script src="/unisono/js/feed.js"></script>
