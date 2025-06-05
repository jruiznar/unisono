// Función comportamiento del modal de invitados en crear evento
function initModalInvitados() {
  const btnAbrir = document.getElementById('btnAgregarInvitados');
  const modal = document.getElementById('modalInvitados');
  const btnCerrar = document.getElementById('cerrarModal');

  if (!btnAbrir || !modal || !btnCerrar) return;
//abrir el modal al hacer clic en el botón "Agregar invitados"
  btnAbrir.addEventListener('click', () => {
    modal.style.display = 'flex';
  });

  btnCerrar.addEventListener('click', () => {
    modal.style.display = 'none';
  });
  //Cerrar el modal si se hace clic fuera del contenido (sobre el fondo)
  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
}
