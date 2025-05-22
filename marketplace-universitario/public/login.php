<?php
require_once "../includes/conexion.php";
session_start();

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    // Validación especial para admin (usando correo válido)
    if ($correo === "admin@admin.com" && $contraseña === "admin") {
        $_SESSION["admin"] = true;
        header("Location: usuarios.php");
        exit;
    }

    // Validación normal para usuarios
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
        if ($usuario['verificado']) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            header("Location: dashboard.php");
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
  <meta charset="UTF-8">
  <title>Iniciar sesión - Marketplace Universitario</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
    }
    header {
      background-color: #1877f2;
      padding: 15px 30px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo-titulo {
      display: flex;
      align-items: center;
    }
    .logo-titulo img {
      height: 40px;
      margin-right: 15px;
    }
    nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: bold;
    }
    .form-container {
      max-width: 400px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #1877f2;
    }
    form {
      margin-top: 20px;
    }
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #1877f2;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background-color: #145dbf;
    }
    p {
      text-align: center;
      color: #d8000c;
      margin-top: 10px;
    }
    a {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #1877f2;
      text-decoration: none;
    }
    footer {
      text-align: center;
      background-color: #e9eff8;
      padding: 20px;
      color: #333;
      margin-top: 50px;
    }
  </style>
</head>
<body>

<header>
  <div class="logo-titulo">
    <img src="../img/logo.svg" alt="Logo del proyecto">
    <h1>Marketplace Universitario</h1>
  </div>
  <nav>
    <a href="index.php">Inicio</a>
    <a href="registro.php">Registrarse</a>
  </nav>
</header>

<div class="form-container">
  <h2>Iniciar sesión</h2>
  <form method="POST">
    <input type="email" name="correo" placeholder="Correo" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
  </form>
  <p><?= $mensaje ?></p>
  <a href="registro.php">¿No tienes cuenta? Regístrate</a>
</div>

<footer>
  © 2025 Marketplace Universitario | <a href="https://unimarketua.blogspot.com/2025/05/un-marketplace-universitario.html">Contáctanos</a>
</footer>

</body>
</html>
