function initPerfilJS() {
    const abrirModalBtn = document.getElementById("abrir-modal-video");
    const cerrarModalBtn = document.getElementById("cerrar-modal");
    const modal = document.getElementById("modal-subir-video");

    if (abrirModalBtn) {
        abrirModalBtn.addEventListener("click", function () {
            modal.classList.remove("oculto");
        });
    }

    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener("click", function () {
            modal.classList.add("oculto");
        });
    }

    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.add("oculto");
        }
    });

    // Cambiar foto perfil
    const btnCambiarFoto = document.getElementById("btn-cambiar-foto");
    const inputCambiarFoto = document.getElementById("input-cambiar-foto");

    if (btnCambiarFoto && inputCambiarFoto) {
        btnCambiarFoto.addEventListener("click", () => {
            inputCambiarFoto.click();
        });

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
                    btnCambiarFoto.src = data.nuevaRuta;
                } else {
                    alert('Error al subir la imagen: ' + data.error);
                }
            })
            .catch(() => alert('Error en la conexión al subir la foto'));
        });
    }
const textareaBiografia = document.getElementById("biografia");

if (textareaBiografia) {
    textareaBiografia.addEventListener("blur", function () {
        const nuevaBiografia = textareaBiografia.value;

        fetch("central/actualizar_biografia.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `biografia=${encodeURIComponent(nuevaBiografia)}`
        })
        .then(response => response.text())
        .then(data => {
            console.log("Biografía actualizada:", data);
        })
        .catch(error => {
            console.error("Error actualizando biografía:", error);
        });
    });
}


}
