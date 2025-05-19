<?php
session_start();
session_unset();
session_destroy(); // Cierra cualquier sesión anterior
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="./css/styles.css" />
</head>
<body>
  <div class="container">
    <!-- Contenedor para imagen y formulario -->
    <div class="login">
      <img class="login-banner" src="./iconos/unisono.png" alt="Banner" />

      <section class="login-container">
        <form class="login-form" method="POST" action="login.php">
          <div class="form-group">
            <img class="logouser" src="./iconos/User.png" alt="Icono usuario" />
            <input type="text" name="nombre_usuario" placeholder="Usuario" required />
          </div>

          <div class="form-group">
            <img class="logokey" src="./iconos/Key.png" alt="Icono contraseña" />
            <input type="password" name="pass" placeholder="Contraseña" required />
          </div>

          <button type="submit" class="btn btn-primary">Entrar</button>

          <p class="register-text">¿Aún no tienes cuenta?</p>
          <button type="button" class="btn btn-secondary" onclick="window.location.href='registro.html';">Regístrate</button>
        </form>
      </section>
    </div>
  </div>
</body>
</html>
