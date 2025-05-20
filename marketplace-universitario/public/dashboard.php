<?php
require_once "../includes/conexion.php";
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
// Obtener promedio de reputación
$stmt = $pdo->prepare("SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE evaluado_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$reputacion = $stmt->fetch();
$promedio = round($reputacion['promedio'], 2);
$total = $reputacion['total'];

$nombre = $_SESSION["nombre"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<script>
fetch("verificar_mensajes.php")
  .then(res => res.json())
  .then(data => {
    if (data.nuevos > 0) {
      document.getElementById("notif").style.display = "inline";
    }
  });
</script>

<body>
    <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h2>

    <ul>
        <li><a href="crear_publicacion.php">➕ Crear publicación</a></li>
        <li><a href="mis_publicaciones.php">📦 Mis publicaciones</a></li>
        <li>📌 <a href="explorar.php">Explorar publicaciones</a></li>
        <li>💬 <a href="chat.php">Mensajes <span id="notif" style="color:red; display:none;">●</span></a></li>
        <li><a href="logout.php">🚪 Cerrar sesión</a></li>
    </ul>
    <p>🌟 Reputación: <?php echo $total > 0 ? "$promedio/5 ($total opiniones)" : "Sin calificaciones aún"; ?></p>

</body>
</html>
