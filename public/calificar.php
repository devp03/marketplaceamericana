<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_GET['publicacion_id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM publicaciones WHERE id = ?");
$stmt->execute([$publicacion_id]);
$pub = $stmt->fetch();

if (!$pub || $pub['reservada'] == 0) {
    echo "❌ Publicación no encontrada o no reservada.";
    exit;
}

$evaluado_id = ($usuario_id == $pub['usuario_id']) ? $pub['reservada_por'] : $pub['usuario_id']; // Vendedor o comprador

// Validar si ya calificó
$stmt = $pdo->prepare("SELECT * FROM calificaciones WHERE evaluador_id = ? AND publicacion_id = ?");
$stmt->execute([$usuario_id, $publicacion_id]);
if ($stmt->rowCount() > 0) {
    echo "✅ Ya has calificado esta transacción.";
    exit;
}

// Guardar calificación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $puntuacion = (int)$_POST["puntuacion"];
    $comentario = trim($_POST["comentario"]);

    if ($puntuacion >= 1 && $puntuacion <= 5) {
        $stmt = $pdo->prepare("INSERT INTO calificaciones (evaluador_id, evaluado_id, publicacion_id, puntuacion, comentario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $evaluado_id, $publicacion_id, $puntuacion, $comentario]);
        echo "✅ ¡Gracias por calificar!";
        exit;
    } else {
        echo "❌ Calificación inválida.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Calificar usuario</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>
<h2>⭐ Califica al <?php echo ($usuario_id == $pub['usuario_id']) ? "comprador" : "vendedor"; ?></h2>

<form method="POST">
    <label for="puntuacion">Puntuación (1-5):</label>
    <input type="number" name="puntuacion" min="1" max="5" required><br><br>

    <label for="comentario">Comentario:</label><br>
    <textarea name="comentario" rows="4" cols="40" required></textarea><br><br>

    <button type="submit">Enviar calificación</button>
</form>

<br><a href="dashboard.php">← Volver al panel</a>
</body>
</html>
