<?php
session_start();
include('../conexion.php');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo "<p>Por favor, introduce un término de búsqueda.</p>";
    exit();
}

$termino = '%' . trim($_GET['q']) . '%';

// Buscar usuarios
$sql_usuarios = "SELECT id_usuario, nombre, apellidos, instrumento, foto_perfil, localidad FROM usuario 
                 WHERE nombre LIKE ? OR apellidos LIKE ? OR nombre_usuario LIKE ?";
$stmt_usuarios = $conn->prepare($sql_usuarios);
$stmt_usuarios->bind_param("sss", $termino, $termino, $termino);
$stmt_usuarios->execute();
$result_usuarios = $stmt_usuarios->get_result();

// Buscar eventos, incluyendo búsqueda por creador (nombre o apellidos)
$sql_eventos = "SELECT e.id_evento, e.titulo, e.descripcion, e.fecha, e.hora, e.ubicacion, e.imagen,
                       u.nombre, u.apellidos 
                FROM evento e
                JOIN usuario u ON e.id_creador = u.id_usuario
                WHERE e.titulo LIKE ? OR e.descripcion LIKE ? OR u.nombre LIKE ? OR u.apellidos LIKE ?";
$stmt_eventos = $conn->prepare($sql_eventos);
$stmt_eventos->bind_param("ssss", $termino, $termino, $termino, $termino);
$stmt_eventos->execute();
$result_eventos = $stmt_eventos->get_result();


// Mapeo instrumento - profesional 
$profesionales = [
    'guitarra' => 'Guitarrista',
    'bajo' => 'Bajista',
    'piano' => 'Pianista',
    'batería' => 'Baterista'
];
?>

<div class="resultados-busqueda">
<h2 class="titulo-resultados">Resultados para: "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>

<div class="filtros-resultados">
    <label class="checkbox-custom">
        <input type="checkbox" id="filtro-perfil" checked>
        <span class="checkmark"></span>
        Perfil
    </label>
    &nbsp;&nbsp;
    <label class="checkbox-custom">
        <input type="checkbox" id="filtro-evento" checked>
        <span class="checkmark"></span>
        Evento
    </label>
</div>

<div class="resultados-usuarios">
    <?php if ($result_usuarios->num_rows > 0): ?>
        <ul class="lista-usuarios">
            <?php while ($usuario = $result_usuarios->fetch_assoc()): ?>
                <?php
                $instrumento = strtolower(trim($usuario['instrumento']));
                $profesional = isset($profesionales[$instrumento]) ? $profesionales[$instrumento] : $usuario['instrumento'];
                ?>
                <li class="usuario-item">
                    <a href="#" class="ver-perfil" data-id="<?php echo $usuario['id_usuario']; ?>">
                        <?php 
                            $foto = !empty($usuario['foto_perfil']) ? '/unisono/uploads/' . $usuario['foto_perfil'] : '/unisono/iconos/perfil.png'; 
                        ?>
                        <img class="foto-perfil" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de <?php echo htmlspecialchars($usuario['nombre']); ?>">
                        <div class="info-usuario">
                            <div class="nombre-localidad">
                                <?php echo htmlspecialchars($usuario['nombre']) . ' ' . htmlspecialchars($usuario['apellidos']) . ', ' . htmlspecialchars($usuario['localidad']); ?>
                            </div>
                            <div class="profesional">
                                <?php echo htmlspecialchars($profesional); ?>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
    <?php endif; ?>
</div>

<div class="resultados-eventos">
    <?php if ($result_eventos->num_rows > 0): ?>
        <ul class="lista-eventos">
            <?php while ($evento = $result_eventos->fetch_assoc()): ?>
<li class="evento-item" data-id-evento="<?php echo $evento['id_evento']; ?>" data-modo="creado">
                    <?php 
                        $imagenEvento = !empty($evento['imagen']) ? '/unisono/imagenes_eventos/' . $evento['imagen'] : '/unisono/iconos/evento.jpg';
                        $nombreCreador = htmlspecialchars($evento['nombre'] . ' ' . $evento['apellidos']);
                    ?>
                    <img class="foto-evento" src="<?php echo htmlspecialchars($imagenEvento); ?>" alt="Imagen del evento <?php echo htmlspecialchars($evento['titulo']); ?>">
                    <div class="info-evento">
                        <h3 class="titulo-evento"><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                        <div class="fecha-hora">
                            <?php echo htmlspecialchars($evento['fecha']) . ' - ' . substr($evento['hora'], 0, 5); ?>
                        </div>
                        <div class="creador-evento">
                            Creado por: <?php echo $nombreCreador; ?>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
    <?php endif; ?>
</div>
</div>

<?php
$stmt_usuarios->close();
$stmt_eventos->close();
$conn->close();
?>
