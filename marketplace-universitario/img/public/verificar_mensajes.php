<?php
session_start();
require_once "../includes/conexion.php";

$id_usuario = $_SESSION['usuario_id'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM mensajes WHERE receptor_id = ? AND leido = 0");
$stmt->execute([$id_usuario]);
$nuevos = $stmt->fetchColumn();

echo json_encode(["nuevos" => $nuevos]);
