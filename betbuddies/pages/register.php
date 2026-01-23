```php
<?php
require "../conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre  = trim($_POST["nombre"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $pass1   = $_POST["password"] ?? "";
    $pass2   = $_POST["password2"] ?? "";

    if (empty($nombre) || empty($email) || empty($pass1) || empty($pass2)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Email no válido.";
    } elseif ($pass1 !== $pass2) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE nombre = :nombre OR email = :email";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            "nombre" => $nombre,
            "email"  => $email
        ]);

        if ($stmt->fetch()) {
            $mensaje = "El usuario o email ya existe.";
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, email, password, rol, saldo)
                    VALUES (:nombre, :email, :password, 'usuario', 100)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                "nombre"   => $nombre,
                "email"    => $email,
                "password" => $hash
            ]);

            header("Location: ../index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | BetBuddies</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<div class="login-card">
    <h2>Registro BetBuddies</h2>

    <?php if ($mensaje): ?>
        <p style="color:red; text-align:center;">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="nombre" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Repetir contraseña</label>
            <input type="password" name="password2" required>
        </div>

        <button type="submit">Crear cuenta</button>

        <div class="extra">
            ¿Ya tienes cuenta?
            <a href="../index.php">Inicia sesión</a>
        </div>

    </form>
</div>

</body>
</html>
```
