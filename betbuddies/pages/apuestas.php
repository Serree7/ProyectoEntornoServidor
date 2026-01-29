<?php
session_start();
require "../conexion.php";

// Seguridad
if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

// Crear apuesta
if (isset($_POST["crear_apuesta"])) {

    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $cantidad = floatval($_POST["cantidad"]);
    $creador_id = $_SESSION["id"];

    if ($titulo === "" || $cantidad <= 0) {
        $error = "Datos inválidos";
    } elseif ($cantidad > $_SESSION["saldo"]) {
        $error = "No tienes saldo suficiente";
    } else {

        $sql = "INSERT INTO apuestas (creador_id, titulo, descripcion, cantidad)
                VALUES (:creador_id, :titulo, :descripcion, :cantidad)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ":creador_id" => $creador_id,
            ":titulo" => $titulo,
            ":descripcion" => $descripcion,
            ":cantidad" => $cantidad
        ]);

        $_SESSION["saldo"] -= $cantidad;
        $success = "Apuesta creada correctamente";
    }
}
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

<!-- ================= APOSTAS ================= -->
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
            <p style="color:#ff4d4d; margin-bottom:15px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color:#00ff99; margin-bottom:15px;">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>

        <form class="form-apuesta" method="POST">
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
                Crear apuesta
            </button>
        </form>
    </article>

    <article class="apuestas__lista card">
        <h3 class="apuestas__subtitulo">Mis apuestas</h3>
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
