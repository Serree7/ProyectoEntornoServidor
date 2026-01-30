let apuestas = document.getElementById("apuestas");
let apuesta = document.getElementById("apuesta");

let configuracionBtn = document.getElementById("configuracionBtn");
let configuracion = document.getElementById("configuracion");

// ----------------- PANEL PRINCIPAL -----------------

apuestas.addEventListener("click", function (e) {
    e.preventDefault();
    apuesta.style.display = "flex";
    if (configuracion) configuracion.style.display = "none";
    
    // Opcional: Quitar clase activa del botón de config si existe
    if (configuracionBtn) configuracionBtn.classList.remove("active");
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

        // --- NUEVO: Gestionar clase activa en el menú ---
        configItems.forEach(li => li.classList.remove("active"));
        item.classList.add("active");

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

// --- NUEVO: Confirmaciones de seguridad para acciones de Admin ---
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function(e) {
        if (!confirm("¿Estás seguro de que deseas realizar esta acción? Esta operación no se puede deshacer.")) {
            e.preventDefault();
        }
    });
});

//Mosstrar contraseña

const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

togglePassword.addEventListener("click", () => {
    password.type = password.type === "password" ? "text" : "password";
    togglePassword.querySelector("i").classList.toggle("fa-eye-slash");
});