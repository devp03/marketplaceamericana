<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marketplace Universitario</title>
    <link rel="stylesheet" href="../public/estilo.css"> <!-- o estilo_unificado.css si lo usás -->
</head>
<body>
<header class="encabezado">
    <div class="encabezado-logo">
        <a href="../public/dashboard.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <img src="../img/logo.svg" alt="Logo">
            <h1>Marketplace Universitario</h1>
        </a>
    </div>
    <nav class="encabezado-nav">
        <a href="../public/crear_publicacion.php">➕ Crear publicación</a>
        <a href="../public/mis_publicaciones.php">📦 Mis publicaciones</a>
        <a href="../public/explorar.php">🔍 Explorar publicaciones</a>
        <a href="../public/chat.php">💬 Mensajes</a>
        <a href="../public/logout.php">🚪 Cerrar sesión</a>

        <?php
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../includes/conexion.php';
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if ($usuario && $usuario['correo'] === 'matias@alumno.ua.edu.py') {
        echo '<a href="/marketplace-universitario/public/wordpress.php" target="_blank">🔐 Página WordPress</a>';
    }
}
?>
<?php
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../includes/conexion.php';
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if ($usuario && $usuario['correo'] === 'matias@alumno.ua.edu.py') {
        echo '<a href="/marketplace-universitario/public/acerca_de_mi.php">👤 Acerca de mí</a>';
    }
}
?>
    </nav>
</header>
<main>

