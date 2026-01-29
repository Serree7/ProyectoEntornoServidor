<?php
session_start();
require "../conexion.php";

// 1. Seguridad: Verificar sesión
if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

/**
 * Función de registro con ESCUDO.
 * Evita el error "Fatal error: Cannot redeclare registrarLog()" cuando se incluye configuracion.php
 */
if (!function_exists('registrarLog')) {
    function registrarLog($conexion, $usuario_id, $accion) {
        $sql = "INSERT INTO logs_actividad (usuario_id, accion) VALUES (:uid, :acc)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['uid' => $usuario_id, 'acc' => $accion]);
    }
}

// 2. Sincronizar saldo real desde la DB (Por si el admin lo cambió manualmente)
$stmtUser = $conexion->prepare("SELECT saldo FROM usuarios WHERE id = :id");
$stmtUser->execute(['id' => $_SESSION["id"]]);
$userDB = $stmtUser->fetch(PDO::FETCH_ASSOC);

if ($userDB) {
    $_SESSION["saldo"] = $userDB["saldo"];
}

$error = "";

// 3. Lógica para CREAR APUESTA
if (isset($_POST["crear_apuesta"])) {

    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $cantidad = floatval($_POST["cantidad"]);
    $creador_id = $_SESSION["id"];

    if ($titulo === "" || $cantidad <= 0) {
        $error = "Datos inválidos.";
    } elseif ($cantidad > $_SESSION["saldo"]) {
        $error = "No tienes saldo suficiente.";
    } else {
        // A. Insertar la apuesta en la tabla
        $sql = "INSERT INTO apuestas (creador_id, titulo, descripcion, cantidad)
                VALUES (:creador_id, :titulo, :descripcion, :cantidad)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ":creador_id" => $creador_id,
            ":titulo" => $titulo,
            ":descripcion" => $descripcion,
            ":cantidad" => $cantidad
        ]);

        // B. REGISTRO DE LOG (Ahora se guardará en la tabla logs_actividad)
        registrarLog($conexion, $creador_id, "Creó una apuesta: '$titulo' por $cantidad €");

        // C. Restar saldo en la Base de Datos
        $upd = $conexion->prepare("UPDATE usuarios SET saldo = saldo - :cant WHERE id = :id");
        $upd->execute([':cant' => $cantidad, ':id' => $creador_id]);

        // D. Actualizar variable de sesión para reflejo inmediato
        $_SESSION["saldo"] -= $cantidad;

        // E. REDIRECCIÓN: Limpia el formulario para evitar duplicados al pulsar F5
        header("Location: apuestas.php?creado=1");
        exit;
    }
}

// 4. Obtener "Mis Apuestas" actualizadas
$sql_mis_apuestas = "SELECT * FROM apuestas WHERE creador_id = :id ORDER BY id DESC";
$stmt_list = $conexion->prepare($sql_mis_apuestas);
$stmt_list->execute([":id" => $_SESSION["id"]]);
$mis_apuestas = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apuestas - BetBuddies</title>
    <link rel="stylesheet" href="../css/apuestas.css">
</head>

<body>

<?php include 'header.php'; ?>

<section id="apuesta" class="apuestas">

    <article class="apuestas__bienvenida card">
        <h2 class="apuestas__titulo">
            🎲 Bienvenido, <?= htmlspecialchars($_SESSION["nombre"]) ?>
        </h2>
        <p class="apuestas__saldo">
            Saldo disponible:
            <span class="saldo__cantidad">
                <?= number_format($_SESSION["saldo"], 2) ?> €
            </span>
        </p>
    </article>

    <article class="apuestas__crear card">
        <h3 class="apuestas__subtitulo">Crear nueva apuesta</h3>

        <?php if (!empty($error)): ?>
            <p style="color:#ff4d4d; margin-bottom:15px; font-weight: bold; background: rgba(255,77,77,0.1); padding: 10px; border-radius: 5px;">
                ⚠️ <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['creado'])): ?>
            <p style="color:#00ff99; margin-bottom:15px; font-weight: bold; background: rgba(0,255,153,0.1); padding: 10px; border-radius: 5px;">
                ✅ ¡Apuesta creada correctamente y registrada en el historial!
            </p>
        <?php endif; ?>

        <form class="form-apuesta" method="POST" action="apuestas.php">
            <div class="form-apuesta__grupo">
                <label class="form-apuesta__label">Título</label>
                <input class="form-apuesta__input" type="text" name="titulo" required>
            </div>

            <div class="form-apuesta__grupo">
                <label class="form-apuesta__label">Descripción</label>
                <textarea class="form-apuesta__input" name="descripcion" rows="3"></textarea>
            </div>

            <div class="form-apuesta__grupo">
                <label class="form-apuesta__label">Cantidad (€)</label>
                <input class="form-apuesta__input" type="number" name="cantidad" min="1" step="0.01" required>
            </div>

            <button class="form-apuesta__btn" type="submit" name="crear_apuesta">
                Confirmar Apuesta
            </button>
        </form>
    </article>

    <article class="apuestas__lista card">
        <h3 class="apuestas__subtitulo">Mis apuestas activas</h3>
        
        <?php if (empty($mis_apuestas)): ?>
            <p style="opacity: 0.6; text-align: center; padding: 20px;">No tienes apuestas registradas.</p>
        <?php else: ?>
            <div class="lista-contenedor">
                <?php foreach ($mis_apuestas as $ap): ?>
                    <div class="apuesta-item" style="border-bottom: 1px solid #333; padding: 15px 0;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?= htmlspecialchars($ap['titulo']) ?></strong>
                            <span style="color: #00ff99; font-weight: bold;">-<?= number_format($ap['cantidad'], 2) ?> €</span>
                        </div>
                        <p style="font-size: 0.85em; margin-top: 5px; opacity: 0.7;">
                            <?= htmlspecialchars($ap['descripcion']) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

</section>

<?php
if ($_SESSION["rol"] === "admin") {
    include 'configuracion.php';
}
?>

<script src="../js/script.js"></script>
</body>
</html>