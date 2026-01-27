let apuestas_panel = document.getElementById("apuestas");
let apuesta = document.getElementById("apuesta");

const mostrarpanelapuestas = () => {
    const estilo = window.getComputedStyle(apuesta).display;
    if(estilo === "none") {
        apuesta.style.display = "grid";
    } else {
        apuesta.style.display = "none";
    }
}

apuestas_panel.addEventListener("click", mostrarpanelapuestas);
