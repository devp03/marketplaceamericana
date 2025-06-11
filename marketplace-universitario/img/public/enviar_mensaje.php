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
$mensaje = trim($_POST['mensaje'] ?? '');

if ($publicacion_id && $otro_id && !empty($mensaje)) {
    $stmt = $pdo->prepare("INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje) VALUES (?, ?, ?, ?)");
    $stmt->execute([$publicacion_id, $usuario_id, $otro_id, $mensaje]);
}

// Redirigir de vuelta al chat con los mismos parámetros
header("Location: chat.php?publicacion_id=$publicacion_id&otro_id=$otro_id");
exit;
?>
