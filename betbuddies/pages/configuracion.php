<?php
// 1. Seguridad: Verificar si es admin
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "admin") {
    exit;
}

require "../conexion.php";

/**
 * Función de registro con ESCUDO para evitar que se rompa el header
 */
if (!function_exists('registrarLog')) {
    function registrarLog($conexion, $usuario_id, $accion) {
        $sql = "INSERT INTO logs_actividad (usuario_id, accion) VALUES (:uid, :acc)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['uid' => $usuario_id, 'acc' => $accion]);
    }
}

// 2. Lógica de Procesamiento (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // ACCIÓN: Ajustar Saldo
    if (isset($_POST['accion']) && $_POST['accion'] === 'ajustar_saldo') {
        $user_id = $_POST['user_id'];
        $nuevo_saldo = $_POST['nuevo_saldo'];
        
        $sql = "UPDATE usuarios SET saldo = :saldo WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['saldo' => $nuevo_saldo, 'id' => $user_id]);

        registrarLog($conexion, $_SESSION["id"], "Editó saldo del usuario ID $user_id a $nuevo_saldo €");
        
        if ($user_id == $_SESSION["id"]) $_SESSION["saldo"] = $nuevo_saldo;
        $msg_success = "Saldo actualizado.";
    }

    // ACCIÓN: Eliminar Usuario
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar_usuario') {
        $user_id = $_POST['user_id'];

        // 1. Opcional pero recomendado: Borrar primero sus apuestas (evita error de FK)
        $sql_del_bets = "DELETE FROM apuestas WHERE creador_id = :id";
        $stmt_bets = $conexion->prepare($sql_del_bets);
        $stmt_bets->execute(['id' => $user_id]);

        // 2. Borrar al usuario (siempre que no sea admin)
        $sql = "DELETE FROM usuarios WHERE id = :id AND rol != 'admin'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['id' => $user_id]);

        // 3. Registrar en el log
        registrarLog($conexion, $_SESSION["id"], "Eliminó al usuario ID $user_id y sus apuestas");

        // 4. ¡ESTO ES LO MÁS IMPORTANTE! Redirigir para limpiar el POST y refrescar la tabla
        header("Location: apuestas.php?admin_msg=Usuario+eliminado");
        exit;
    }
    // ACCIÓN: Eliminar Apuesta
    if (isset($_POST['accion']) && $_POST['accion'] === 'cerrar_apuesta') {
        $apuesta_id = $_POST['apuesta_id'];
        $sql = "DELETE FROM apuestas WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['id' => $apuesta_id]);

        registrarLog($conexion, $_SESSION["id"], "Eliminó la apuesta ID $apuesta_id");
        $msg_success = "Apuesta eliminada.";
    }
}

// 3. Obtener datos para las tablas
$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$apuestas_totales = $conexion->query("SELECT a.*, u.nombre as creador FROM apuestas a JOIN usuarios u ON a.creador_id = u.id ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener logs (unimos con usuarios para ver el nombre de quien hizo la acción)
$logs = $conexion->query("
    SELECT l.*, u.nombre 
    FROM logs_actividad l 
    JOIN usuarios u ON l.usuario_id = u.id 
    ORDER BY l.fecha DESC LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/config.css">

<section id="configuracion" class="apuestas" style="display:none;">

    <article class="card">
        <h2 class="apuestas__titulo">⚙️ Panel Admin</h2>
        <?php if(isset($msg_success)): ?>
            <p style="color: #00ff99; text-align:center;"><?= $msg_success ?></p>
        <?php endif; ?>

        <ul class="config-menu">
            <li class="menu-item active" data-panel="panel-apuestas">📊 Apuestas</li>
            <li class="menu-item" data-panel="panel-usuarios">👥 Usuarios</li>
            <li class="menu-item" data-panel="panel-saldos">💰 Saldos</li>
            <li class="menu-item" data-panel="panel-logs">📜 Ver Logs</li>
        </ul>
    </article>

    <article id="panel-apuestas" class="card config-panel">
        <h3>Gestión de Apuestas</h3>
        <table class="admin-table">
            <thead><tr><th>Título</th><th>Monto</th><th>Acción</th></tr></thead>
            <tbody>
                <?php foreach ($apuestas_totales as $ap): ?>
                <tr>
                    <td><?= htmlspecialchars($ap['titulo']) ?></td>
                    <td><?= $ap['cantidad'] ?>€</td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="apuesta_id" value="<?= $ap['id'] ?>">
                            <input type="hidden" name="accion" value="cerrar_apuesta">
                            <button type="submit" class="btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </article>

    <article id="panel-usuarios" class="card config-panel" style="display:none;">
        <h3>Usuarios</h3>
        <table class="admin-table">
            <thead><tr><th>Nombre</th><th>Acción</th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td>
                        <?php if($u['rol'] !== 'admin'): ?>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">    
                            <input type="hidden" name="accion" value="eliminar_usuario">
                            <button type="submit" class="btn-delete">Borrar</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </article>

    <article id="panel-saldos" class="card config-panel" style="display:none;">
        <h3>Editar Saldos</h3>
        <?php foreach ($usuarios as $u): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; padding:5px; border-bottom:1px solid #333;">
                <span><?= htmlspecialchars($u['nombre']) ?> (<?= $u['saldo'] ?>€)</span>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="accion" value="ajustar_saldo">
                    <input type="number" name="nuevo_saldo" step="0.01" style="width:70px;">
                    <button type="submit">Ok</button>
                </form>
            </div>
        <?php endforeach; ?>
    </article>

    <article id="panel-logs" class="card config-panel" style="display:none;">
        <h3>📜 Historial de Acciones</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($l['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($l['accion']) ?></td>
                    <td style="font-size:0.8em; opacity:0.6;"><?= $l['fecha'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </article>

</section>