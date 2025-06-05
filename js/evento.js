document.addEventListener('DOMContentLoaded', () => {
    const botonCrearEvento = document.getElementById('crear-evento-btn');
    if (botonCrearEvento) {
        botonCrearEvento.addEventListener('click', () => {
            cargarCrearEvento();
        });
    }

    // La pongo en el calendario?
});

/**
 * Carga el formulario crearEvento.php y luego inicializa sus JS
 * @param {number} id_evento - ID del evento (opcional)
 * @param {string} modo - 'creado' o 'invitado' (opcional)
 */
function cargarCrearEvento(id_evento = 0, modo = 'creado') {
    let url = 'central/crearEvento.php';
    const params = [];
    if (id_evento > 0) params.push('id_evento=' + encodeURIComponent(id_evento));
    if (modo) params.push('modo=' + encodeURIComponent(modo));
    if (params.length) url += '?' + params.join('&');

    // Realizar la petición fetch
    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('Error cargando crearEvento.php');
            return res.text();
        })
        .then(html => {
            // Insertar el HTML en el contenedor central
            document.getElementById('central-container').innerHTML = html;
            initCrearEvento(modo); 
        })
        .catch(err => {
            console.error('Error al cargar crearEvento.php:', err);
        });
}

/**
 * Inicializa el JS para el formulario crearEvento.php
 * @param {string} modo - 'creado' o 'invitado'
 */
function initCrearEvento(modo = 'creado') {
    // --- Modal Invitados ---
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
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('mostrar');
                modal.classList.add('oculto');
            }
        });
    }

    // --- Imagen preview y clic para abrir input file ---
    const contenedorImagen = document.getElementById('imagenEventoContainer');
    const inputImagenEvento = document.getElementById('imagenEvento');
    const imagenPreview = document.getElementById('imagenPreview');

    if (contenedorImagen && inputImagenEvento && imagenPreview) {
        if (modo === 'invitado') {
            inputImagenEvento.disabled = true;
            contenedorImagen.style.cursor = 'default';
        } else {
            inputImagenEvento.disabled = false;
            contenedorImagen.style.cursor = 'pointer';
            contenedorImagen.addEventListener('click', () => {
                inputImagenEvento.click();
            });
            inputImagenEvento.addEventListener('change', () => {
                const archivo = inputImagenEvento.files[0];
                if (!archivo) return;
                const reader = new FileReader();
                reader.onload = e => {
                    imagenPreview.src = e.target.result;
                };
                reader.readAsDataURL(archivo);
            });
        }
    }

    // --- Botón borrar evento ---
    const btnBorrar = document.getElementById('btnBorrarEvento');
    if (btnBorrar) {
        btnBorrar.addEventListener('click', () => {
            if (confirm("¿Seguro que quieres borrar este evento? Esta acción no se puede deshacer.")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/unisono/central/borrar_evento.php';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id_evento';

                const inputHidden = document.querySelector('input[name="id_evento"]');
                if (inputHidden) {
                    inputId.value = inputHidden.value;
                } else {
                    alert('No se encontró el ID del evento para borrar.');
                    return;
                }

                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // --- Deshabilitar campos si es modo invitado ---
    if (modo === 'invitado') {
        const campos = document.querySelectorAll('input, textarea, button');
        campos.forEach(campo => {
            if (
                campo.id !== 'btnAgregarInvitados' &&
                campo.id !== 'btnBorrarEvento'
            ) {
                campo.disabled = true;
            }
        });
    }
}
