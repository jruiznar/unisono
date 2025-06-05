<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo "No has iniciado sesión.";
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$foto = !empty($usuario['foto_perfil']) ? '/unisono/uploads/' . $usuario['foto_perfil'] : '/unisono/iconos/perfil.png';
$tecnicas = !empty($usuario['tecnicas']) ? json_decode($usuario['tecnicas'], true) : [];
if (!is_array($tecnicas)) $tecnicas = [];


// Videos del usuario
$sql_videos = "SELECT * FROM video WHERE id_usuario = ? ORDER BY fecha_subida DESC";
$stmt_videos = $conn->prepare($sql_videos);
$stmt_videos->bind_param("i", $id_usuario);
$stmt_videos->execute();
$result_videos = $stmt_videos->get_result();
?>

<?php

$modoFeed = 'miPerfil';
// include 'feed.php';

?>


<div class="datosPerfil">
    <div class="modificar">
        <div class="foto-contenedor" id="btn-cambiar-foto">
            <div class="cambiar-foto-texto">Cambiar<br>foto de perfil</div>
            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" class="foto-modificar">
        </div>
        <input type="file" id="input-cambiar-foto" accept="image/*" style="display:none;">
        <button class="boton-editar"><label id="texto-perfil">Editar Perfil </label></button>
    </div>

    <div class="instrumento-biografia">
    <div class="fila-botones-perfil">
        <button id="btn-anadir-instrumento"><label id="texto-instrumento">Añadir instrumento</label></button>
        <div class="seguidores-seguidos">
            <button class="btn-seguidos">Seguidos</button>
            <button class="btn-seguidores">Seguidores</button>
        </div>
    </div>

    <div class="instrumento-actual"><?php echo htmlspecialchars($usuario['instrumento']); ?></div>
    <textarea id="biografia" class="biografia" placeholder="Biografía"><?php echo htmlspecialchars($usuario['biografia']); ?></textarea>
</div>

</div>



<div class="tecnicas">
    <h3>Técnicas</h3>
    <div class="grupo-etiquetas" id="lista-tecnicas">
        <?php
        $max_visibles = 5;
        foreach ($tecnicas as $i => $tecnica): ?>
            <span class="etiqueta<?= $i >= $max_visibles ? ' oculta' : '' ?>">
                <?= htmlspecialchars($tecnica) ?>
            </span>
        <?php endforeach; ?>

        <button class="etiqueta btn-anadir" id="btn-anadir-tecnica">+ Añadir</button>

        <?php if(count($tecnicas) > $max_visibles): ?>
            <button class="etiqueta btn-ver-mas-perfil">Ver más</button>
        <?php endif; ?>
    </div>
</div>


<div class="feed">
    <h3>Publicaciones</h3>
    <div class="video-miniatura subir-video" id="abrir-modal-video">
            <div class="icono-subir">+</div>
        </div>
   <?php 
$desdePerfilPropio = true; 
$ocultarDatosUsuario = true;
include('feed.php'); 
?>

</div>





<!-- Modal para subir video -->
<div id="modal-subir-video" class="modal oculto">
    <div class="modal-contenido">
        <span id="cerrar-modal" class="cerrar">&times;</span>
        <h3>Subir Video</h3>
        <form id="form-subir-video" action="/unisono/central/subir_video.php" method="POST" enctype="multipart/form-data">
            <!-- <label for="video">Selecciona un archivo de video:</label> -->
            <input type="file" name="video" accept="video/*" required>
            <textarea name="descripcion" placeholder="Descripción (opcional)"></textarea>
            <button type="submit">Subir</button>
        </form>
    </div>
</div>

<!-- Modal para seguidores / seguidos -->
<div id="modal-seguidores" class="modal oculto">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalSeguidores()">&times;</span>
        <h3 id="modal-titulo">Usuarios</h3>
        <ul id="lista-usuarios">
            <!-- Se llena con JS -->
        </ul>
    </div>
</div>

<script src="../js/feed.js"></script>
<script src="../js/perfil.js"></script>
