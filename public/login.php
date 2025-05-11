<?php
require_once "../includes/conexion.php";
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
        if ($usuario['verificado']) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            header("Location: dashboard.php"); // redirige al panel principal
            exit;
        } else {
            $mensaje = "❌ Tu cuenta no ha sido verificada aún.";
        }
    } else {
        $mensaje = "❌ Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <form method="POST">
        <input type="email" name="correo" placeholder="Correo" required><br><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
    <p><?php echo $mensaje; ?></p>
    <a href="registro.php">¿No tienes cuenta? Regístrate</a>
</body>
</html>
