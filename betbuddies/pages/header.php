<?php
session_start();

if (!isset($_SESSION["id"])) {
  header("Location: ../index.php");
  exit;
}

$rol = $_SESSION["rol"];
$nombre = $_SESSION["nombre"];
$inicial = strtoupper(substr($nombre, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BetBuddies</title>
  <link rel="stylesheet" href="../css/header.css" />
</head>

<body>

  <header>
    <div class="logo">BetBuddies</div>

    <div class="user-info">
      <!-- Inicial del usuario -->
      <div class="user-avatar" id="userAvatar">
        <?= htmlspecialchars($inicial) ?>
      </div>


      <!-- Nombre del usuario -->
      <div class="user-name" id="userName">
        <p><?= htmlspecialchars($nombre) ?></p>
      </div>


      <!-- Botón solo para admin -->
      <?php if ($rol === "admin"): ?>
        <div class="user-rol">
          <a href="./configuracion.php" class="config">Configuración</a>
        </div>
      <?php endif; ?>


      <a id="apuestas" class="close_session" href="./apuestas.php">Apuestas</a>

      <a class="close_session" href="../logout.php">Cerrar Sesión</a>
    </div>
  </header>

  <script src="../js/script.js"></script>

</body>

</html>