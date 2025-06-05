<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$id_evento = isset($_GET['id_evento']) ? intval($_GET['id_evento']) : 0;

// Valores iniciales para evento
$evento = [
    'titulo' => '',
    'ubicacion' => '',
    'fecha' => '',
    'hora' => '',
    'descripcion' => '',
    'imagen' => '',
    'id_creador' => null,
];

if ($id_evento > 0) {
    $sqlEvento = "SELECT * FROM evento WHERE id_evento = ? LIMIT 1";
    $stmtEvento = $conn->prepare($sqlEvento);
    $stmtEvento->bind_param("i", $id_evento);
    $stmtEvento->execute();
    $resultEvento = $stmtEvento->get_result();

    if ($resultEvento && $resultEvento->num_rows > 0) {
        $evento = $resultEvento->fetch_assoc();
    }
    $stmtEvento->close();

    $nombreCreador = '';
if (!empty($evento['id_creador'])) {
    $stmtCreador = $conn->prepare("SELECT nombre, apellidos FROM usuario WHERE id_usuario = ?");
    $stmtCreador->bind_param("i", $evento['id_creador']);
    $stmtCreador->execute();
    $resultCreador = $stmtCreador->get_result();
    if ($resultCreador && $resultCreador->num_rows > 0) {
        $creador = $resultCreador->fetch_assoc();
        $nombreCreador = $creador['nombre'] . ' ' . $creador['apellidos'];
    }
    $stmtCreador->close();
}

}

$modo = 'invitado'; // por defecto invitado 
if ($id_evento > 0) {
    // Evento existente: comprueba si eres creador
    $modo = ($evento['id_creador'] === $id_usuario) ? 'creado' : 'invitado';
} else {
    // Evento nuevo: eres creador
    $modo = 'creado';
}


$disabled = ($modo === 'invitado') ? 'disabled' : '';
$mostrarBotones = ($modo === 'creado');

// Consulta seguidores solo si es creador para mostrar en modal
$resultSeguidores = null;
if ($mostrarBotones) {
    $sqlSeguidores = "
        SELECT u.id_usuario, u.nombre, u.apellidos 
        FROM seguimiento s 
        JOIN usuario u ON s.seguidor_id = u.id_usuario 
        WHERE s.seguido_id = ?
    ";
    $stmtSeguidores = $conn->prepare($sqlSeguidores);
    $stmtSeguidores->bind_param("i", $id_usuario);
    $stmtSeguidores->execute();
    $resultSeguidores = $stmtSeguidores->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $id_evento > 0 ? "Editar Evento" : "Crear Evento"; ?></title>
    <link rel="stylesheet" href="/unisono/css/stylesPrincipal.css" />
    <link rel="stylesheet" href="/unisono/css/crearEvento.css" />
</head>
<body>
<div class="crear-evento-container">
    <form action="/unisono/central/guardar_evento.php" method="POST" enctype="multipart/form-data">
        <?php if ($id_evento > 0): ?>
            <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
        <?php endif; ?>

        <div class="datos">
            <div id="imagenEventoContainer" style="cursor:pointer; display:inline-block;">
                <img id="imagenPreview" src="<?php echo !empty($evento['imagen']) ? '/unisono/imagenes_eventos/' . $evento['imagen'] : '/unisono/iconos/evento.jpg'; ?>" alt="Imagen Evento" class="imagen-evento" />
            </div>
            <input type="file" id="imagenEvento" name="imagenEvento" accept="image/*" style="display:none;" <?php echo $disabled; ?> />

            <div class="inputs">

            
    <label for="nombreCreador">Creador:</label>
    <?php if ($modo === 'creado'): ?>
        <div class="campo">
    <!-- <label for="nombreCreador">Creador:</label> -->
    <input type="text" id="nombreCreador" 
           value="<?php echo htmlspecialchars($modo === 'creado' ? $_SESSION['nombre_usuario'] : $nombreCreador); ?>" 
           readonly>
    <?php if ($modo === 'creado'): ?>
        <input type="hidden" name="id_creador" value="<?php echo $_SESSION['id_usuario']; ?>">
    <?php endif; ?>
</div>


    <?php endif; ?>

    <div class="campo">
        <input type="text" id="nombreEvento" name="nombreEvento" placeholder="Nombre del evento"
               value="<?php echo htmlspecialchars($evento['titulo'] ?? ''); ?>" required <?php echo $disabled; ?> />
    </div>
    <div class="campo">
        <textarea id="ubicacionEvento" name="ubicacionEvento" placeholder="Ubicación"
                  required <?php echo $disabled; ?>><?php echo htmlspecialchars($evento['ubicacion'] ?? ''); ?></textarea>
    </div>
    <div class="campo">
        <input type="date" id="fechaEvento" name="fechaEvento"
               value="<?php echo htmlspecialchars($evento['fecha'] ?? ''); ?>" required <?php echo $disabled; ?> />
    </div>
    <div class="campo">
        <input type="time" id="horaEvento" name="horaEvento"
               value="<?php echo htmlspecialchars($evento['hora'] ?? ''); ?>" required <?php echo $disabled; ?> />
    </div>
</div>


        </div>

        <div class="descripcion">
            <label for="descripcionEvento">Descripción</label>
            <textarea id="descripcionEvento" name="descripcionEvento" placeholder="Descripción del evento..." <?php echo $disabled; ?>><?php echo htmlspecialchars($evento['descripcion'] ?? ''); ?></textarea>
        </div>

        <?php if ($mostrarBotones): ?>
        <div class="botones">
            <button type="submit" class="crear-btn">Guardar</button>
            <button type="button" class="invitados-btn" id="btnAgregarInvitados">Añadir Invitados</button>
            <?php if ($id_evento > 0): ?>
                <button type="button" id="btnBorrarEvento" class="borrar-btn">Borrar Evento</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($mostrarBotones && $resultSeguidores): ?>
        <div id="modalInvitados" class="modal oculto">
            <div class="modal-contenido">
                <span class="cerrar" id="cerrarModal">&times;</span>
                <h3>Selecciona a quién invitar:</h3>
                <div class="lista-invitados">
                    <?php while ($row = $resultSeguidores->fetch_assoc()): ?>
                        <label>
                            <input type="checkbox" name="invitados[]" value="<?php echo $row['id_usuario']; ?>">
                            <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?>
                        </label><br>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
