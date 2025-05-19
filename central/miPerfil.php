<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    echo "No has iniciado sesión.";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$foto = !empty($usuario['foto_perfil']) ? '/unisono/uploads/' . $usuario['foto_perfil'] : '/unisono/perfil.png';

// Videos del usuario
$sql_videos = "SELECT * FROM video WHERE id_usuario = ? ORDER BY fecha_subida DESC";
$stmt_videos = $conn->prepare($sql_videos);
$stmt_videos->bind_param("i", $usuario_id);
$stmt_videos->execute();
$result_videos = $stmt_videos->get_result();
?>
<link rel="stylesheet" href="../css/stylePerfil.css" /> 

<div class="datos">
    <div class="modificar">
        <div class="foto-contenedor" id="btn-cambiar-foto">
            <div class="cambiar-foto-texto">Cambiar<br>foto de perfil</div>
            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" class="foto-modificar">
        </div>
        <input type="file" id="input-cambiar-foto" accept="image/*" style="display:none;">
        <button class="boton-editar">Editar Perfil</button>
    </div>

    <div class="instrumento-biografia">
        <button id="btn-anadir-instrumento">Añadir instrumento</button>
        <div class="instrumento-actual"><?php echo htmlspecialchars($usuario['instrumento']); ?></div>
        <textarea id="biografia" class="biografia" placeholder="Biografía"><?php echo htmlspecialchars($usuario['biografia']); ?></textarea>
    </div>
</div>



<div class="tecnicas">
    <h3>Técnicas</h3>
    <div class="lista-etiquetas">
        <button class="etiqueta btn-anadir">+ Añadir</button>
        <span class="etiqueta">Técnica 1</span>
        <span class="etiqueta">Técnica 2</span>
        <span class="etiqueta">Técnica 3</span>
        <span class="etiqueta">Técnica 4</span>
        <span class="etiqueta">Técnica 5</span>
        <button class="etiqueta btn-ver-mas">Ver más</button>
    </div>
</div>

<div class="publicaciones">
    <h3>Publicaciones</h3>
    <div class="grid-videos">
        <!-- Botón para subir video -->
        <div class="video-miniatura subir-video" id="abrir-modal-video">
            <div class="icono-subir">+</div>
        </div>

        <?php while ($video = $result_videos->fetch_assoc()): ?>
            <div class="video-miniatura">
<video src="/unisono/<?php echo htmlspecialchars($video['url_video']); ?>" controls controlsList="nodownload"></video>
            </div>
        <?php endwhile; $stmt_videos->close(); ?>
    </div>
</div>

<!-- Modal para subir video -->
<div id="modal-subir-video" class="modal oculto">
    <div class="modal-contenido">
        <span id="cerrar-modal" class="cerrar">&times;</span>
        <h3>Subir Video</h3>
        <form id="form-subir-video" action="/unisono/central/subir_video.php" method="POST" enctype="multipart/form-data">
            <label for="video">Selecciona un archivo de video:</label>
            <input type="file" name="video" accept="video/*" required>
            <textarea name="descripcion" placeholder="Descripción (opcional)"></textarea>
            <button type="submit">Subir</button>
        </form>
    </div>
</div>

<script src="../js/perfil.js"></script>
