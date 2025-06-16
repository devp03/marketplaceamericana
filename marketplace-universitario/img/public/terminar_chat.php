<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_POST['publicacion_id'] ?? null;
$otro_id = $_POST['otro_id'] ?? null;

if ($publicacion_id && $otro_id) {
    // Marcar el chat como terminado
    $stmt = $pdo->prepare("UPDATE reservas SET chat_terminado = 1 WHERE publicacion_id = ? AND (usuario_id = ? OR ? = (SELECT usuario_id FROM publicaciones WHERE id = ?))");
    $stmt->execute([$publicacion_id, $usuario_id, $usuario_id, $publicacion_id]);

    // Redirigir al formulario de calificación
    header("Location: calificar.php?publicacion_id=$publicacion_id&otro_id=$otro_id");
    exit;
}

header("Location: chat.php");
exit;
?>
