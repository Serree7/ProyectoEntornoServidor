<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

$nombre = $_SESSION["nombre"];
$inicial = strtoupper(substr($nombre, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BetBuddies</title>
  <link rel="stylesheet" href="../css/header.css"/>
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

    <a class="close_session" href="../logout.php">Cerrar Sesión</a>
  </div>
</header>

</body>
</html>
