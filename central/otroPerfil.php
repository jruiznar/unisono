<?php
session_start();
//var_dump(session_id(), $_SESSION);

include('../conexion.php');

if (!isset($_GET['id'])) {
    echo "Perfil no especificado.";
    exit();
}

$perfil_id = intval($_GET['id']);

//var_dump($_SESSION['id_usuario'], $perfil_id);



// Obtener datos del usuario
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $perfil_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

$foto = !empty($usuario['foto_perfil']) ? '/unisono/uploads/' . $usuario['foto_perfil'] : '/unisono/iconos/perfil.png';

// Obtener videos del usuario
$sql_videos = "SELECT * FROM video WHERE id_usuario = ? ORDER BY fecha_subida DESC";
$stmt_videos = $conn->prepare($sql_videos);
$stmt_videos->bind_param("i", $perfil_id);
$stmt_videos->execute();
$result_videos = $stmt_videos->get_result();


$edad = isset($usuario['edad']) && $usuario['edad'] !== null ? intval($usuario['edad']) : 'N/D';
?>

<link rel="stylesheet" href="../css/otroPerfil.css" />

<div class="contenedor-perfil">
    <div class="fila-superior">
        <div class="columna-izquierda">
            <div class="info-otroperfil">
                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" class="foto-otro">
                <div class="datos-usuario">
                    <div class="nombre-edad">
                        <?= htmlspecialchars($usuario['nombre']) ?>, <?= $edad ?>
                    </div>
                    <div class="municipio">
                        <?= htmlspecialchars($usuario['localidad'] ?? 'N/D') ?>
                    </div>
                 <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != $perfil_id): ?>
    <?php
    // Verificar si ya sigue al usuario
    $sql_check = "SELECT 1 FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $_SESSION['id_usuario'], $perfil_id);
    $stmt_check->execute();
    $ya_sigue = $stmt_check->get_result()->num_rows > 0;
    $stmt_check->close();
    ?>
    <button class="btn-seguir" data-id="<?= $perfil_id ?>">
    <?= $ya_sigue ? 'Siguiendo' : 'Seguir' ?>
</button>

<?php endif; ?>
                </div>
                
            </div>
            <div class="biografiaotro">
                <?= nl2br(htmlspecialchars($usuario['biografia'])) ?>
            </div>
        </div>
<?php
// Mapeo instrumento → profesional (igual que en home.php)
$profesionales = [
    'guitarra' => 'Guitarrista',
    'bajo' => 'Bajista',
    'piano' => 'Pianista',
    'batería' => 'Baterista'
];

$instrumento = strtolower(trim($usuario['instrumento'] ?? ''));

$profesional = isset($profesionales[$instrumento]) ? $profesionales[$instrumento] : $usuario['instrumento'];
?>
<?php
$modoFeed = 'otroPerfil';
$perfilIDParaFeed = $perfil_id; // id del perfil visitado
$ocultarDatosUsuario = true; // Oculta foto/nombre en videos dentro del perfil
// include('feed.php');
?>
        <div class="columna-derecha">
           <div class="instrumento-otro">
<p><span class="profesional-label"><?= htmlspecialchars($profesional) ?></span></p>
</div>



            <div class="tecnicas-otro">
    <h3>Técnicas</h3>
    <div class="grupo-etiquetas">
        <?php
        $tecnicas = [];
        if (!empty($usuario['tecnicas'])) {
            $tecnicas = json_decode($usuario['tecnicas'], true);
            if (!is_array($tecnicas)) $tecnicas = [];
        }

        if (count($tecnicas) > 0):
            $max_visibles = 3;
            foreach ($tecnicas as $i => $tecnica):
        ?>
            <span class="etiqueta<?= $i >= $max_visibles ? ' oculta' : '' ?>">
                <?= htmlspecialchars($tecnica) ?>
            </span>
        <?php
            endforeach;
            if (count($tecnicas) > $max_visibles):
        ?>
            <button class="etiqueta btn-ver-mas" onclick="mostrarMasTecnicas(this)">Ver más</button>
        <?php
            endif;
        else:
        ?>
            <span class="etiqueta">Por registrar</span>
        <?php endif; ?>
    </div>
</div>

        </div>
    </div>
<div class="feed">
    <div class="publicaciones-otro">
    <!-- <h3>Publicaciones</h3> -->
    <?php
    $perfilIDParaFeed = $perfil_id;
    $ocultarDatosUsuario = true;
    include('feed.php');
    ?>
    </div>
</div>
</div>
<script src="/unisono/js/seguir.js"></script>
<script src="/unisono/js/tecnicas.js"></script>
<script src="../js/feed.js"></script>


