document.addEventListener('DOMContentLoaded', () => {
  const btnExit = document.getElementById('btn-exit');
  if (btnExit) {
    btnExit.addEventListener('click', () => {
      window.location.href = '/unisono/logout.php'; // Redirige al cerrar sesión
    });
  }
});
