<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION["id"])) {
  header("Location: ../index.php");
  exit;
}

$rol = $_SESSION["rol"];
$nombre = $_SESSION["nombre"];
$inicial = strtoupper(substr($nombre, 0, 1));
?>
<link rel="stylesheet" href="../css/header.css">

<header>
  <div class="logo">BetBuddies</div>

  <div class="user-info">

    <div class="user-avatar" id="userAvatar">
      <?= htmlspecialchars($inicial) ?>
    </div>

    <div class="user-name" id="userName">
      <p><?= htmlspecialchars($nombre) ?></p>
    </div>

    <a id="apuestas" class="close_session" href="">Apuestas</a>

    <?php if ($rol === "admin"): ?>
      <a id="configuracionBtn" class="close_session" href="">Configuración</a>
    <?php endif; ?>

    <a class="close_session" href="../logout.php">Cerrar Sesión</a>

  </div>
</header>
