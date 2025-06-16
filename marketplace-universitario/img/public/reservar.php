<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publicacion_id = $_POST["publicacion_id"];
    $usuario_id = $_SESSION['usuario_id'];

    // 🟢 Marcar la publicación como reservada
    $stmt = $pdo->prepare("UPDATE publicaciones SET reservada = 1, reservada_por = ? WHERE id = ?");
    $stmt->execute([$usuario_id, $publicacion_id]);

    // 🟠 Registrar la reserva en la tabla 'reservas'
    $stmt2 = $pdo->prepare("INSERT INTO reservas (publicacion_id, usuario_id) VALUES (?, ?)");
    $stmt2->execute([$publicacion_id, $usuario_id]);

    // Redireccionar al explorador después de reservar
    header("Location: explorar.php");
    exit;
}
?>