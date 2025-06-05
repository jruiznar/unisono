document.addEventListener("DOMContentLoaded", () => {
    // --- Elementos del DOM y variables globales ---
  const diasContainer = document.getElementById("calendario-dias");
  const mesActualLabel = document.getElementById("mes-actual");
  const btnPrev = document.getElementById("prev-month");
  const btnNext = document.getElementById("next-month");
  const modal = document.getElementById("modal-eventos");
  const btnCerrarModal = document.getElementById("cerrarModalEventos");

  let fechaActual = new Date();
  // --- Función para renderizar el calendario ---
  function renderizarCalendario() {
    const año = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();
    const primerDiaMes = new Date(año, mes, 1);
    const ultimoDiaMes = new Date(año, mes + 1, 0);
    const diasEnMes = ultimoDiaMes.getDate();

    let primerDiaSemana = primerDiaMes.getDay();
    if (primerDiaSemana === 0) primerDiaSemana = 7;

    diasContainer.innerHTML = "";

    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                   "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    mesActualLabel.textContent = `${meses[mes]} ${año}`;

    for (let i = 1; i < primerDiaSemana; i++) {
      const espacio = document.createElement("div");
      espacio.classList.add("dia-vacio");
      diasContainer.appendChild(espacio);
    }

    for (let dia = 1; dia <= diasEnMes; dia++) {
      const diaDiv = document.createElement("div");
      diaDiv.textContent = dia;
      diaDiv.classList.add("dia-normal");

      const fechaFormateada = `${año}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
      diaDiv.setAttribute("data-fecha", fechaFormateada);

      diaDiv.addEventListener("click", () => {
        mostrarEventosDeFecha(fechaFormateada);
      });

      diasContainer.appendChild(diaDiv);
    }
  }
  // --- Función para cargar eventos desde el servidor ---
  async function cargarEventosCalendario() {
    try {
      const res = await fetch("/unisono/obtener_eventos.php");
      const eventos = await res.json();

      eventos.forEach(evento => {
        const celda = document.querySelector(`[data-fecha="${evento.fecha}"]`);
        if (celda) {
          celda.classList.remove("rojo", "amarillo");
          celda.classList.add(evento.color);
        }
      });
    } catch (error) {
      console.error("Error al cargar eventos del calendario:", error);
    }
  }

  btnPrev.addEventListener("click", () => {
    fechaActual.setDate(1);
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    renderizarCalendario();
    cargarEventosCalendario();
  });

  btnNext.addEventListener("click", () => {
    fechaActual.setDate(1);
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    renderizarCalendario();
    cargarEventosCalendario();
  });

  renderizarCalendario();
  cargarEventosCalendario();

  function mostrarEventosDeFecha(fecha) {
    fetch(`/unisono/obtener_eventos.php?fecha=${fecha}`)
      .then(res => res.json())
      .then(eventos => {
        const contenedor = document.getElementById("contenido-modal-eventos");
        const titulo = document.getElementById("titulo-modal-eventos");

        titulo.textContent = `Eventos para el día ${fecha}`;
        contenedor.innerHTML = "";

        if (eventos.length === 0) {
          contenedor.innerHTML = "<p>No hay eventos para este día.</p>";
        } else {
          const lista = document.createElement("ul");

          eventos.forEach(ev => {
            const item = document.createElement("li");
            if (ev.tipo === 'creado') {
              item.classList.add("evento-creado");
            } else if (ev.tipo === 'invitado') {
              item.classList.add("evento-invitado");
            }

            const img = document.createElement("img");
            img.src = ev.imagen_url || "/unisono/iconos/evento.jpg";
            img.alt = ev.titulo || "Evento";

            const texto = document.createTextNode(ev.titulo || '(Sin título)');

            item.appendChild(img);
            item.appendChild(texto);
            item.style.cursor = "pointer";

            item.addEventListener('click', () => {
              fetch(`/unisono/central/crearEvento.php?id_evento=${ev.id_evento}&modo=${ev.tipo}`)
                .then(response => response.text())
                .then(html => {
                  document.getElementById("central-container").innerHTML = html;

                  // Funcion que está en evento.js
                  if (typeof initCrearEvento === 'function') {
                    initCrearEvento(ev.tipo);
                  }

                  cerrarModalEventos();
                })
                .catch(error => {
                  console.error("Error al cargar formulario de edición:", error);
                });
            });

            lista.appendChild(item);
          });

          contenedor.appendChild(lista);
        }

        modal.classList.add("mostrar");
        modal.classList.remove("oculto");
      })
      .catch(error => {
        console.error("Error al cargar eventos:", error);
      });
  }

  window.cerrarModalEventos = function () {
    modal.classList.remove("mostrar");
    modal.classList.add("oculto");
  };

  if (btnCerrarModal) {
    btnCerrarModal.addEventListener("click", () => {
      cerrarModalEventos();
    });
  }

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      cerrarModalEventos();
    }
  });
});
