<?php
require_once "../includes/conexion.php";
session_start();
include '../includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_GET['publicacion_id'] ?? null;
$otro_id = $_GET['otro_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $puntuacion = $_POST['puntuacion'];
    $comentario = $_POST['comentario'];

    $stmt = $pdo->prepare("INSERT INTO calificaciones (publicacion_id, calificador_id, evaluado_id, puntuacion, comentario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$publicacion_id, $usuario_id, $otro_id, $puntuacion, $comentario]);

    header("Location: dashboard.php");
    exit;
}
?>

<form method="POST">
    <h3>Calificar a <?php echo htmlspecialchars($otro_id); ?> por la publicación <?php echo $publicacion_id; ?></h3>
    <label for="puntuacion">Puntuación (1 a 5):</label>
    <input type="number" name="puntuacion" min="1" max="5" required>
    <br>
    <label for="comentario">Comentario:</label><br>
    <textarea name="comentario" rows="4" cols="50" required></textarea><br><br>
    <button type="submit">Enviar Calificación</button>
</form>
<?php include '../includes/footer.php'; ?>

