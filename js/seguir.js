document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-seguir')) {
            const button = e.target;
            const seguidoId = button.getAttribute('data-id');// Obtiene el ID del usuario a seguir o dejar de seguir


            fetch('/unisono/central/seguir.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `seguido_id=${encodeURIComponent(seguidoId)}`,
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'seguido') {
                    button.textContent = 'Siguiendo';// Cambia el texto del botón
                } else if (data.status === 'no_seguido') {
                    button.textContent = 'Seguir';
                } else if (data.status === 'error') {
                    alert(data.message || 'Error desconocido');
                } else {
                    console.error('Respuesta inesperada:', data);
                }
            })
            .catch(err => {
                console.error('Error en fetch:', err);
                alert('Error en la conexión, inténtalo de nuevo.');
            });
        }
    });
});
