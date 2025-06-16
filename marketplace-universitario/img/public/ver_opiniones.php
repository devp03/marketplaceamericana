
<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_GET['id'])) {
    echo "❌ Publicación no especificada.";
    exit;
}

$pub_id = $_GET['id'];

// Obtener información de la publicación
$stmt_pub = $pdo->prepare("SELECT * FROM publicaciones WHERE id = ?");
$stmt_pub->execute([$pub_id]);
$publicacion = $stmt_pub->fetch();

if (!$publicacion) {
    echo "❌ Publicación no encontrada.";
    exit;
}

// Obtener opiniones
$stmt = $pdo->prepare("
    SELECT c.puntuacion, c.comentario, u.nombre, c.fecha
    FROM calificaciones c
    JOIN usuarios u ON u.id = c.calificador_id
    WHERE c.publicacion_id = ?
    ORDER BY c.fecha DESC
");
$stmt->execute([$pub_id]);
$opiniones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Opiniones de <?php echo htmlspecialchars($publicacion['titulo']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #1877f2;
        }
        .opinion {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .opinion strong {
            color: #333;
        }
        .opinion small {
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Opiniones de "<?php echo htmlspecialchars($publicacion['titulo']); ?>"</h2>

    <?php if (count($opiniones) > 0): ?>
        <?php foreach ($opiniones as $op): ?>
            <div class="opinion">
                <p><strong><?php echo htmlspecialchars($op['nombre']); ?></strong> — ⭐ <?php echo $op['puntuacion']; ?>/5</p>
                <p><?php echo htmlspecialchars($op['comentario']); ?></p>
                <small><?php echo date("d/m/Y H:i", strtotime($op['fecha'])); ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay opiniones aún para esta publicación.</p>
    <?php endif; ?>

    <a href="explorar.php" style="display:inline-block; margin-top: 20px;">← Volver a publicaciones</a>
</body>
</html>
