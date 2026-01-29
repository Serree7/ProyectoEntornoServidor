let apuestas = document.getElementById("apuestas");
let apuesta = document.getElementById("apuesta");

let configuracionBtn = document.getElementById("configuracionBtn");
let configuracion = document.getElementById("configuracion");

// ----------------- PANEL PRINCIPAL -----------------

apuestas.addEventListener("click", function (e) {
    e.preventDefault();
    apuesta.style.display = "block";
    if (configuracion) configuracion.style.display = "none";
});

if (configuracionBtn) {
    configuracionBtn.addEventListener("click", function (e) {
        e.preventDefault();
        apuesta.style.display = "none";
        configuracion.style.display = "block";
    });
}

// ----------------- SUBMENÚ CONFIG -----------------

let configItems = document.querySelectorAll(".config-menu li");
let configPanels = document.querySelectorAll(".config-panel");

configItems.forEach(item => {
    item.addEventListener("click", () => {

        // Ocultar todos los subpaneles
        configPanels.forEach(panel => {
            panel.style.display = "none";
        });

        // Mostrar el panel correspondiente
        let panelId = item.getAttribute("data-panel");
        let panel = document.getElementById(panelId);

        if (panel) {
            panel.style.display = "block";
        }
    });
});
