document.addEventListener('DOMContentLoaded', function () {
    console.log('busqueda.js cargado');

    const input = document.getElementById('cuadro-busqueda'); // input de búsqueda
    const botonLupa = document.querySelector('.boton-lupa'); // buscar por clase
    const central = document.getElementById('central-container'); // contenedor resultados

    if (!input || !central) return;

    function hacerBusqueda() {
        const query = input.value.trim();

        if (query.length > 2) {
            fetch(`central/busqueda.php?q=${encodeURIComponent(query)}`)
                .then(res => res.text())
                .then(html => {
                    central.innerHTML = html;

                    const filtroPerfil = document.getElementById('filtro-perfil');
                    const filtroEvento = document.getElementById('filtro-evento');
                    const resultadosUsuarios = central.querySelector('.resultados-usuarios');
                    const resultadosEventos = central.querySelector('.resultados-eventos');

                    function actualizarFiltros() {
                        resultadosUsuarios.style.display = filtroPerfil.checked ? 'block' : 'none';
                        resultadosEventos.style.display = filtroEvento.checked ? 'block' : 'none';
                    }

                    actualizarFiltros();

                    if (filtroPerfil) filtroPerfil.addEventListener('change', actualizarFiltros);
                    if (filtroEvento) filtroEvento.addEventListener('change', actualizarFiltros);
                })
                .catch(err => {
                    central.innerHTML = '<p>Error al cargar los resultados.</p>';
                    console.error(err);
                });
        } else {
            central.innerHTML = '';
        }
    }

    input.addEventListener('input', hacerBusqueda);

    if (botonLupa) {
        botonLupa.addEventListener('click', function (e) {
            e.preventDefault();
            hacerBusqueda();
        });
    }

    // Escuchar clicks en perfiles
    document.addEventListener('click', function(e) {
        const perfilLink = e.target.closest('.ver-perfil');
        if (perfilLink) {
            e.preventDefault();
            const id = perfilLink.dataset.id;
            console.log('Click en perfil id:', id);

fetch('central/otroPerfil.php?id=' + encodeURIComponent(id), {
    credentials: 'include'
})
              .then(res => res.text())
              .then(html => {
                central.innerHTML = html;
              })
              .catch(err => {
                console.error('Error al cargar otroPerfil:', err);
              });
        }
    });

    document.addEventListener('click', function(e) {
  
  // Reultado eventos
  const eventoItem = e.target.closest('.evento-item');
  if (eventoItem) {
    e.preventDefault();
    const idEvento = eventoItem.dataset.idEvento;
    const modo = eventoItem.dataset.modo || 'creado'; // por defecto creado

    if (!idEvento) return;

    fetch(`central/crearEvento.php?id_evento=${encodeURIComponent(idEvento)}&modo=${encodeURIComponent(modo)}`)
      .then(res => res.text())
      .then(html => {
        const central = document.getElementById('central-container');
        central.innerHTML = html;

        if (typeof initCrearEvento === 'function') {
          initCrearEvento(modo);
        }
      })
      .catch(err => {
        console.error('Error al cargar crearEvento desde búsqueda:', err);
      });
  }
});

});
