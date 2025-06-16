<?php
require_once "../includes/conexion.php";
session_start();

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "❌ Usuario no encontrado.";
    exit;
}

// Obtener reputación
$stmt = $pdo->prepare("SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE evaluado_id = ?");
$stmt->execute([$id]);
$rep = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8"><title>Perfil de <?php echo htmlspecialchars($user['nombre']); ?></title></head>
<body>
<h2>👤 Perfil de <?php echo htmlspecialchars($user['nombre']); ?></h2>

<p>Correo: <?php echo htmlspecialchars($user['correo']); ?></p>
<p>Reputación: <?php echo $rep['total'] > 0 ? round($rep['promedio'], 2) . "/5 ({$rep['total']} opiniones)" : "Sin calificaciones"; ?></p>

<h3>📦 Publicaciones activas:</h3>
<ul>
<?php
$stmt = $pdo->prepare("SELECT * FROM publicaciones WHERE usuario_id = ? AND reservada = 0");
$stmt->execute([$id]);
foreach ($stmt as $pub) {
    echo "<li><a href='explorar.php?publicacion_id={$pub['id']}'>" . htmlspecialchars($pub['titulo']) . "</a></li>";
}
?>
</ul>

<a href="explorar.php">← Volver</a>
</body>
</html>
