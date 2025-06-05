function initPerfilJS() {
    // --- Código para abrir/cerrar modal subir video ---
    const abrirModalBtn = document.getElementById("abrir-modal-video");
    const cerrarModalBtn = document.getElementById("cerrar-modal");
    const modal = document.getElementById("modal-subir-video");

    if (abrirModalBtn) {
        abrirModalBtn.addEventListener("click", () => modal.classList.remove("oculto"));
    }

    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener("click", () => modal.classList.add("oculto"));
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("oculto");
        }
    });

    // --- Cambiar foto perfil ---
    const btnCambiarFoto = document.getElementById("btn-cambiar-foto");
    const inputCambiarFoto = document.getElementById("input-cambiar-foto");

    if (btnCambiarFoto && inputCambiarFoto) {
        btnCambiarFoto.addEventListener("click", () => inputCambiarFoto.click());

        inputCambiarFoto.addEventListener("change", () => {
            const archivo = inputCambiarFoto.files[0];
            if (!archivo) return;

            const formData = new FormData();
            formData.append('foto_perfil', archivo);

            fetch('central/subir_foto_perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Actualizar imagen en el DOM
                    btnCambiarFoto.querySelector('img').src = data.nuevaRuta;
                } else {
                    alert('Error al subir la imagen: ' + data.error);
                }
            })
            .catch(() => alert('Error en la conexión al subir la foto'));
        });
    }

    // --- Actualizar biografía ---
    const textareaBiografia = document.getElementById("biografia");

    if (textareaBiografia) {
        textareaBiografia.addEventListener("blur", () => {
            const nuevaBiografia = textareaBiografia.value;

            fetch("central/actualizar_biografia.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `biografia=${encodeURIComponent(nuevaBiografia)}`
            })
            .then(response => response.text())
            .then(data => console.log("Biografía actualizada:", data))
            .catch(error => console.error("Error actualizando biografía:", error));
        });
    }

    // --- Botones seguidores / seguidos ---
    const btnSeguidores = document.querySelector(".btn-seguidores");
    const btnSeguidos = document.querySelector(".btn-seguidos");

    if (btnSeguidores) {
        btnSeguidores.addEventListener("click", () => cargarUsuarios("seguidores"));
    }

    if (btnSeguidos) {
        btnSeguidos.addEventListener("click", () => cargarUsuarios("seguidos"));
    }

    // --- Añadir técnica ---
    const btnAnadir = document.getElementById("btn-anadir-tecnica");
    const listaTecnicas = document.getElementById("lista-tecnicas");

    if (btnAnadir && listaTecnicas) {
        btnAnadir.addEventListener("click", () => {
            const nuevaTecnica = prompt("Añade una técnica nueva:");
            if (nuevaTecnica && nuevaTecnica.trim() !== "") {
                const existe = Array.from(listaTecnicas.querySelectorAll("span.etiqueta"))
                    .some(span => span.textContent.toLowerCase() === nuevaTecnica.trim().toLowerCase());

                if (existe) {
                    alert("Esta técnica ya está añadida.");
                    return;
                }

                const span = document.createElement("span");
                span.className = "etiqueta";
                span.textContent = nuevaTecnica.trim();
                listaTecnicas.insertBefore(span, btnAnadir);

                fetch("/unisono/central/actualizar_tecnicas.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `tecnica=${encodeURIComponent(nuevaTecnica.trim())}`
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert("Error al guardar técnica");
                        span.remove();
                    }
                })
                .catch(() => {
                    alert("Error en la conexión");
                    span.remove();
                });
            }
        });
    }

    // --- Ver más / Ver menos técnicas ---
    const btnVerMas = document.querySelector('.btn-ver-mas-perfil');
    if (btnVerMas) {
        btnVerMas.addEventListener('click', () => toggleVerMasTecnicas(btnVerMas));
    }
    document.querySelectorAll('.btn-comentarios').forEach(button => {
    button.addEventListener('click', () => {
        
    });
});

}

function toggleVerMasTecnicas(boton) {
    const grupo = boton.parentElement;
    const etiquetas = Array.from(grupo.querySelectorAll('.etiqueta'));
    const maxVisibles = 5;

    if (boton.textContent === "Ver más") {
        etiquetas.forEach(etiqueta => etiqueta.classList.remove('oculta'));
        boton.textContent = "Ver menos";
    } else {
        // Ocultar todas las técnicas excepto las primeras 5
        let visibles = 0;
        etiquetas.forEach(etiqueta => {
            if (etiqueta.tagName !== "SPAN") return; 
            if (visibles < maxVisibles) {
                etiqueta.classList.remove('oculta');
                visibles++;
            } else {
                etiqueta.classList.add('oculta');
            }
        });
        boton.textContent = "Ver más";
        grupo.scrollIntoView({ behavior: 'smooth' });
    }
}


// --- Cargar lista de seguidores o seguidos ---
function cargarUsuarios(tipo) {
    const lista = document.getElementById("lista-usuarios");
    const titulo = document.getElementById("modal-titulo");

    fetch(`/unisono/central/obtener_${tipo}.php`)
        .then(res => res.json())
        .then(data => {
            titulo.textContent = tipo === "seguidores" ? "Seguidores" : "Seguidos";
            lista.innerHTML = "";

            if (data.length === 0) {
                lista.innerHTML = "<li>No hay usuarios</li>";
            } else {
                data.forEach(usuario => {
                    const li = document.createElement("li");
                    const enlace = document.createElement("a");
                    enlace.href = "#";
                    enlace.addEventListener("click", e => {
                        e.preventDefault();
                        fetch(`/unisono/central/otroPerfil.php?id=${usuario.id_usuario}`)
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById("central-container").innerHTML = html;
                                window.history.pushState({}, "", `/unisono/otroPerfil.php?id=${usuario.id_usuario}`);
                            })
                            .catch(err => console.error("Error al cargar el perfil:", err));
                    });
                    //estilos para seguidores y seguidos en modal
                    enlace.style.display = "flex";
                    enlace.style.alignItems = "center";
                    enlace.style.gap = "10px";
                    enlace.style.textDecoration = "none";
                    enlace.style.color = "inherit";

                    const img = document.createElement("img");
                    img.src = usuario.foto_perfil;
                    img.alt = usuario.nombre_usuario;
                    img.style.width = "40px";
                    img.style.height = "40px";
                    img.style.borderRadius = "50%";
                    img.style.objectFit = "cover";

                    const span = document.createElement("span");
                    span.textContent = usuario.nombre_usuario;

                    // Estructura HTML
                    enlace.appendChild(img);
                    enlace.appendChild(span);
                    li.appendChild(enlace);
                    lista.appendChild(li);
                });
            }
            // Mostrar el modal de seguidores/seguidos
            document.getElementById("modal-seguidores").classList.remove("oculto");
        });
}
// --- Función global para cerrar el modal de seguidores ---
window.cerrarModalSeguidores = function() {
    document.getElementById("modal-seguidores").classList.add("oculto");
};

// --- Ejecutar el JS de perfil cuando cargue el DOM ---
document.addEventListener("DOMContentLoaded", initPerfilJS);
