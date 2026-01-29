<?php
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "admin") {
    exit;
}
?>

<link rel="stylesheet" href="../css/config.css">

<section id="configuracion" class="apuestas" style="display:none;">

    <article class="card">
        <h2 class="apuestas__titulo">⚙️ Configuración</h2>

        <!-- MENÚ CONFIG -->
        <ul class="config-menu">
            <li data-panel="panel-apuestas">📊 Gestión de apuestas</li>
            <li data-panel="panel-usuarios">👥 Gestión de usuarios</li>
            <li data-panel="panel-saldos">💰 Control de saldos</li>
            <li data-panel="panel-estadisticas">📈 Estadísticas</li>
            <li data-panel="panel-reportes">🚫 Apuestas reportadas</li>
        </ul>
    </article>

    <!-- ================= SUBPANELES ================= -->

    <article id="panel-apuestas" class="card config-panel" style="display:none;">
        <h3>📊 Gestión de apuestas</h3>
        <p>Aquí podrás crear, cerrar o cancelar apuestas.</p>
        <!-- aquí irán tus formularios -->
    </article>

    <article id="panel-usuarios" class="card config-panel" style="display:none;">
        <h3>👥 Gestión de usuarios</h3>
        <p>Banear usuarios, cambiar roles, ver actividad.</p>
    </article>

    <article id="panel-saldos" class="card config-panel" style="display:none;">
        <h3>💰 Control de saldos</h3>
        <p>Ajustar saldos manualmente.</p>
    </article>

    <article id="panel-estadisticas" class="card config-panel" style="display:none;">
        <h3>📈 Estadísticas</h3>
        <p>Visualizar ganancias y pérdidas.</p>
    </article>

    <article id="panel-reportes" class="card config-panel" style="display:none;">
        <h3>🚫 Apuestas reportadas</h3>
        <p>Revisar apuestas denunciadas.</p>
    </article>

</section>
