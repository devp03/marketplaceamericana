<?php
require_once "../includes/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $pass = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);

    // Validar correo institucional
    if (preg_match("/@alumno\.ua\.edu\.py$/", $correo)) {
        // Verifica si ya existe el correo
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);

        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contraseña, verificado) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $correo, $pass, true]); // Simulamos verificación como automática
            $mensaje = "✅ Registro exitoso. Ya puedes iniciar sesión.";
        } else {
            $mensaje = "⚠️ El correo ya está registrado.";
        }
    } else {
        $mensaje = "❌ Solo se permiten correos institucionales (@alumno.ua.edu.py)";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required><br><br>
        <input type="email" name="correo" placeholder="Correo institucional" required><br><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br><br>
        <button type="submit">Registrarse</button>
    </form>
    <p><?php echo $mensaje; ?></p>
    <a href="login.php">¿Ya tienes cuenta? Iniciar sesión</a>
</body>
</html>
