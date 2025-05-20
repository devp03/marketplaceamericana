<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC");
$stmt->execute([$usuario_id]);
$mis_publicaciones = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8">
    <title>Mis publicaciones</title>
</head>
<body>
    <h2>📦 Mis publicaciones</h2>

    <?php foreach ($mis_publicaciones as $pub): ?>
        <div style="border:1px solid #ccc; margin:10px; padding:10px;">
            <h3><?php echo htmlspecialchars($pub['titulo']); ?></h3>
            <p><?php echo htmlspecialchars($pub['descripcion']); ?></p>
            <p>💰 Precio: <?php echo $pub['precio']; ?></p>
            <p>Estado: <?php echo $pub['reservada'] ? "🔒 Reservada" : "🟢 Disponible"; ?></p>
        </div>
    <?php endforeach; ?>

    <br><a href="dashboard.php">← Volver al panel</a>
</body>
</html>
