document.addEventListener('DOMContentLoaded', () => {
    // Busca el botón con id 'btn-del' (botón para eliminar cuenta)
  const btnDel = document.getElementById('btn-del');
  if (btnDel) {
    btnDel.addEventListener('click', () => {
      if (confirm("¿Estás seguro que quieres eliminar tu cuenta? Esta acción no se puede deshacer.")) {
          // Realiza una solicitud POST a eliminarUsuario.php para borrar la cuenta
          fetch('/unisono/eliminarUsuario.php', {
          method: 'POST',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert("Tu cuenta ha sido eliminada.");
            window.location.href = '/unisono/index.php'; // Redirigir al login
          } else {
            alert("Error al eliminar la cuenta: " + (data.error || 'Error desconocido'));
          }
        })
        .catch(error => {
          alert("Error en la conexión: " + error.message);
        });
      }
    });
  }
});
