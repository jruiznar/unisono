// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
  // Selecciona el botón "Ver más"
  const botonVerMas = document.querySelector('.btn-ver-mas');

  // Si el botón existe, agrega el evento
  if (botonVerMas) {
    botonVerMas.addEventListener('click', function () {
      // Mostrar todos los usuarios ocultos
      const usuariosOcultos = document.querySelectorAll('.usuario-sugerido.oculta');
      usuariosOcultos.forEach(usuario => {
        usuario.classList.remove('oculta');
      });

      // Ocultar el botón "Ver más" después de mostrar
      botonVerMas.style.display = 'none';
    });
  }
});
