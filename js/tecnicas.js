// Muestra todas las técnicas 
function mostrarMasTecnicas(boton) {
    const grupo = boton.parentElement;
        // Selecciona todas las etiquetas dentro del grupo, excepto el propio botón "ver más"
    const etiquetas = Array.from(grupo.querySelectorAll('.etiqueta:not(.btn-ver-mas)'));
    const maxVisibles = 3;

    if (boton.textContent === "Ver más") {
        etiquetas.forEach(etiqueta => etiqueta.classList.remove('oculta'));
        boton.textContent = "Ver menos";
    } else {
        etiquetas.forEach((etiqueta, index) => {
            if (index < maxVisibles) {
                etiqueta.classList.remove('oculta');
            } else {
                etiqueta.classList.add('oculta');
            }
        });
        boton.textContent = "Ver más";
        grupo.scrollIntoView({ behavior: 'smooth' });// Desplaza suavemente hasta el grupo
    }
}
