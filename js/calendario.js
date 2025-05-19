document.addEventListener("DOMContentLoaded", () => {
  const diasContainer = document.getElementById("calendario-dias");
  const mesActualLabel = document.getElementById("mes-actual");
  const btnPrev = document.getElementById("prev-month");
  const btnNext = document.getElementById("next-month");

  let fechaActual = new Date();

  function renderizarCalendario() {
    const año = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();

    const primerDiaMes = new Date(año, mes, 1);
    const ultimoDiaMes = new Date(año, mes + 1, 0);
    const diasEnMes = ultimoDiaMes.getDate();

    // Ajuste para que el primer día sea lunes 
    let primerDiaSemana = primerDiaMes.getDay();
    if (primerDiaSemana === 0) primerDiaSemana = 7;

    diasContainer.innerHTML = "";

    // Nombre del mes y año
    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                   "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    mesActualLabel.textContent = `${meses[mes]} ${año}`;

    // Espacios en blanco para alinear el primer lunes
    for (let i = 1; i < primerDiaSemana; i++) {
      const espacio = document.createElement("div");
      espacio.classList.add("dia-vacio");
      diasContainer.appendChild(espacio);
    }

    // Días del mes
    for (let dia = 1; dia <= diasEnMes; dia++) {
      const diaDiv = document.createElement("div");
      diaDiv.textContent = dia;
      diaDiv.classList.add("dia-normal");
      diasContainer.appendChild(diaDiv);
    }
  }

  btnPrev.addEventListener("click", () => {
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    renderizarCalendario();
  });

  btnNext.addEventListener("click", () => {
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    renderizarCalendario();
  });

  // Render inicial
  renderizarCalendario();
});
