//Preview de imagen y  modal de invitados
document.addEventListener('DOMContentLoaded', () => {
  // Imagen preview y input
  const imgPreview = document.getElementById('imagenPreview');
  const inputFile = document.getElementById('imagenEvento');

  if (imgPreview && inputFile) {
    imgPreview.addEventListener('click', () => {
      if (!inputFile.disabled) {
        inputFile.click();
      }
    });

    inputFile.addEventListener('change', e => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          imgPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Modal invitados
  const modal = document.getElementById('modalInvitados');
  const btnInvitados = document.getElementById('btnAgregarInvitados');
  const spanCerrar = document.getElementById('cerrarModal');

  if (btnInvitados && modal && spanCerrar) {
    btnInvitados.addEventListener('click', () => {
      modal.classList.remove('oculto');
      modal.classList.add('mostrar');
    });

    spanCerrar.addEventListener('click', () => {
      modal.classList.remove('mostrar');
      modal.classList.add('oculto');
    });

    window.addEventListener('click', (event) => {
      if (event.target == modal) {
        modal.classList.remove('mostrar');
        modal.classList.add('oculto');
      }
    });
  }
});
