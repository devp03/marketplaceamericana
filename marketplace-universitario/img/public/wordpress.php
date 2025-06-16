<?php
session_start();
require_once "../includes/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if ($usuario && $usuario['correo'] === 'matias@alumno.ua.edu.py') {
    header("Location: http://172.25.6.193/wordpress");
    exit;
} else {
    echo "❌ No tienes permiso para acceder a esta página.";
    exit;
}
?>
