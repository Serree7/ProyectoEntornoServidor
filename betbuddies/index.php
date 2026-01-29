<?php
session_start();
require "conexion.php";

$mensaje = "";

/**
 * Función de registro con ESCUDO para evitar errores de duplicidad
 */
if (!function_exists('registrarLog')) {
    function registrarLog($conexion, $usuario_id, $accion) {
        $sql = "INSERT INTO logs_actividad (usuario_id, accion) VALUES (:uid, :acc)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['uid' => $usuario_id, 'acc' => $accion]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger datos
    $nombre = trim($_POST["nombre"] ?? "");
    $pass   = trim($_POST["password"] ?? "");

    // Validar campos vacíos
    if (empty($nombre) || empty($pass)) {
        $mensaje = "Por favor, rellene todos los campos.";
    } else {

        // Buscar usuario
        $sql = "SELECT * FROM usuarios WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(["nombre" => $nombre]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña
        if ($user && password_verify($pass, $user["password"])) {

            // Guardar datos en sesión
            $_SESSION["id"]     = $user["id"];
            $_SESSION["nombre"] = $user["nombre"];
            $_SESSION["email"]  = $user["email"];
            $_SESSION["rol"] = strtolower(trim($user["rol"]));
            $_SESSION["saldo"]  = $user["saldo"];

            // --- REGISTRO DE LOG: Inicio de sesión ---
            registrarLog($conexion, $user["id"], "Inició sesión en el sistema");

            // Cookie segura
            setcookie(
                "nombre",
                $user["nombre"],
                time() + 3600,
                "/",
                "",
                isset($_SERVER["HTTPS"]),
                true
            );

            // Redirección
            header("Location: ./pages/apuestas.php");
            exit;

        } else {
            $mensaje = "Nombre o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BetBuddies | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/variables.css">
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>

<div class="bg-scene">
    <div class="roulette-container">
        <div class="roulette-wheel"></div>
    </div>
</div>
<div class="overlay-light"></div>

<div class="login-card">
    <h2>BetBuddies</h2>

    <?php if (!empty($mensaje)): ?>
        <p style="color:red; text-align:center;">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Usuario</label>
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" name="nombre" required>
            </div>
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <div class="input-box">
                <i class="fas fa-key"></i>
                <input type="password" name="password" id="password" required>
                <span id="togglePassword" class="password-toggle">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
        </div>

        <button type="submit">Hacer mi apuesta</button>

        <div class="extra">
            ¿Aún sin fichas?
            <a href="./pages/register.php">Únete al club</a>
        </div>
    </form>
</div>

<script src="js/script.js">
</script>

</body>
</html>