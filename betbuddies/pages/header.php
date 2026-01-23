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
      <!-- Iniciales del usuario -->
      <div class="user-avatar" id="userAvatar">J</div>
      <!-- Nombre y apellidos -->
      <div class="user-name" id="userName">
        <p>Juan Pérez García </p>
        <a style="color: white" href="../index.php">Cerrar Sesion</a>
      </div>
    </div>
   
  </header>

  <script>
    // Simulación de datos del usuario tras el login
    const user = {
      nombre: 'Juan',
      apellidos: 'Pérez García'
    };

    // Mostrar nombre completo
    document.getElementById('userName').textContent = `${user.nombre} ${user.apellidos}`;

    // Mostrar iniciales en el avatar
    const iniciales = user.nombre.charAt(0) + user.apellidos.charAt(0);
    document.getElementById('userAvatar').textContent = iniciales.toUpperCase();
  </script>

</body>
</html>
