<?php
require_once "../includes/conexion.php";
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION["nombre"];
$mensaje = "";
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

// Obtener reputación
$stmt = $pdo->prepare("SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE evaluado_id = ?");
$stmt->execute([$usuario_id]);
$reputacion = $stmt->fetch();
$promedio = round($reputacion['promedio'], 2);
$total = $reputacion['total'];
?>

<?php include "../includes/header.php"; ?>

<h2>👋 Bienvenido, <?php echo htmlspecialchars($nombre); ?></h2>
<?php if ($mensaje): ?>
    <p id="mensaje-exito" style="text-align: center; background: #d4edda; color: #155724; padding: 10px; border-radius: 8px;">
        <?php echo $mensaje; ?>
    </p>
    <script>
        setTimeout(() => {
            const mensaje = document.getElementById("mensaje-exito");
            if (mensaje) {
                mensaje.style.transition = "opacity 0.5s ease";
                mensaje.style.opacity = 0;
                setTimeout(() => mensaje.remove(), 500);
            }
        }, 4000);
    </script>
<?php endif; ?>

<p style="text-align:center;">🌟 Reputación: <?php echo $total > 0 ? "$promedio/5 ($total opiniones)" : "Sin calificaciones aún"; ?></p>

<h2>🏍️ Todas las publicaciones disponibles</h2>

<div style="display: flex; flex-wrap: wrap; justify-content: center;">
<?php
$stmt = $pdo->query("SELECT p.*, u.nombre as vendedor FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id");
$publicaciones = $stmt->fetchAll();

foreach ($publicaciones as $pub): ?>
    <div class="card">
        <?php if ($pub['imagen']): ?>
            <img src="../uploads/<?php echo $pub['imagen']; ?>" alt="Imagen de <?php echo htmlspecialchars($pub['titulo']); ?>">
        <?php else: ?>
            <img src="../uploads/default.png" alt="Sin imagen">
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($pub['titulo']); ?></h3>
        <p><?php echo htmlspecialchars($pub['descripcion']); ?></p>
        <span class="precio">Gs <?php echo number_format($pub['precio'], 0, ',', '.'); ?></span>
        <p>👤 <strong>Vendedor:</strong> <?php echo htmlspecialchars($pub['vendedor']); ?></p>
        <a href="explorar.php?destacada=<?php echo $pub['id']; ?>">
            <button style="margin-top: 10px;">Ver más</button>
        </a>
    </div>
<?php endforeach; ?>
</div>

<?php include "../includes/footer.php"; ?>
