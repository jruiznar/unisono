document.addEventListener('DOMContentLoaded', () => {
    // Escuchar todos los botones de "Ver comentarios"
    document.querySelectorAll('.btn-comentarios').forEach(button => {
        button.addEventListener('click', () => {
            const idVideo = button.dataset.id;
            const contenedor = document.getElementById(`comentarios-${idVideo}`);

            // Alternar visibilidad del contenedor
            contenedor.classList.toggle('visible');

            // Si ya está visible y cargado, no recargar
            if (contenedor.classList.contains('visible') && contenedor.dataset.loaded !== '1') {
                fetch(`/unisono/central/get_comentarios.php?id_video=${idVideo}`)
                    .then(res => {
                        if (!res.ok) throw new Error("Error al obtener comentarios");
                        return res.json();
                    })
                    .then(data => {
                        if (data.length === 0) {
                            contenedor.innerHTML = "<p>No hay comentarios todavía.</p>";
                        } else {
                         // Generar HTML con los comentarios recibidos
                            contenedor.innerHTML = `
                               <ul class="lista-comentarios">
    ${data.map(c => `
        <li>
            <img class="comentario-foto" src="/unisono/uploads/${c.foto_perfil}" alt="Foto de perfil de ${c.nombre}">
            <span><strong>${c.nombre}:</strong> ${c.comentario}</span>
        </li>`).join('')}
</ul>
`;
                        }
                        contenedor.dataset.loaded = '1'; /// Marcar como cargado para no volver a pedirlo
                        console.log('Comentarios cargados:', data);
                    })
                    .catch(err => {
                        contenedor.innerHTML = "<p>Error al cargar los comentarios.</p>";
                        console.error(err);
                    });
            }
        });
    });
});
document.addEventListener('click', function(e) {
    const perfilLink = e.target.closest('.ver-perfil');
    if (perfilLink) {
        e.preventDefault();
        const id = perfilLink.dataset.id;
        console.log('Click en perfil id:', id);

        const central = document.getElementById('central-container') || document.querySelector('.feed');
        
        // Petición para obtener y cargar el perfil de otro usuario
        fetch('/unisono/central/otroPerfil.php?id=' + encodeURIComponent(id), {
            credentials: 'include'
        })
        .then(res => res.text())
        .then(html => {
            if (central) {
                central.innerHTML = html;
            }
        })
        .catch(err => {
            console.error('Error al cargar otroPerfil:', err);
        });
    }
});
