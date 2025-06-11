<?php
require_once "../includes/conexion.php";
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;
$publicacion_id = $_GET['publicacion_id'] ?? null;
$otro_id = $_GET['otro_id'] ?? null;

if (!$usuario_id || !$publicacion_id || !$otro_id) {
    exit("❌ Faltan datos.");
}

// Obtener mensajes
$stmt = $pdo->prepare("
    SELECT * FROM mensajes 
    WHERE publicacion_id = ? 
      AND ((remitente_id = ? AND destinatario_id = ?) OR (remitente_id = ? AND destinatario_id = ?)) 
    ORDER BY fecha ASC
");
$stmt->execute([$publicacion_id, $usuario_id, $otro_id, $otro_id, $usuario_id]);
$mensajes = $stmt->fetchAll();

// Marcar como leídos los mensajes entrantes no leídos
$marcarLeidos = $pdo->prepare("
    UPDATE mensajes 
    SET leido = 1 
    WHERE publicacion_id = ? AND destinatario_id = ? AND leido = 0
");
$marcarLeidos->execute([$publicacion_id, $usuario_id]);

// Mostrar mensajes
foreach ($mensajes as $msg) {
    $clase = ($msg['remitente_id'] == $usuario_id) ? 'mensaje-enviado' : 'mensaje-recibido';
    echo "<div class='$clase'>" . htmlspecialchars($msg['mensaje']) . "</div>";
}
?>
