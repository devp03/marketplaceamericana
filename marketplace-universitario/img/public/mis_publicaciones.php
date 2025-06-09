<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Eliminar publicación si se solicitó
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $pub_id = $_POST['eliminar_id'];
    $stmt = $pdo->prepare("DELETE FROM publicaciones WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$pub_id, $usuario_id]);
    header("Location: mis_publicaciones.php");
    exit;
}

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
            <p>💰 Precio: Gs. <?php echo number_format($pub['precio'], 0, ',', '.'); ?></p>
            <p>Estado: <?php echo $pub['reservada'] ? "🔒 Reservada" : "🟢 Disponible"; ?></p>
            <?php if (!empty($pub['imagen'])): ?>
                <img src="../uploads/<?php echo $pub['imagen']; ?>" alt="Imagen" width="150"><br>
            <?php endif; ?>
            <br>
            <a href="editar_publicacion.php?id=<?php echo $pub['id']; ?>">
                <button>✏️ Editar</button>
            </a>
            <form method="POST" style="display:inline-block;">
                <input type="hidden" name="eliminar_id" value="<?php echo $pub['id']; ?>">
                <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar esta publicación?');">🗑️ Eliminar</button>
            </form>
        </div>
    <?php endforeach; ?>

    <br><a href="dashboard.php">← Volver al panel</a>
</body>
</html>
