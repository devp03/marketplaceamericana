<?php
require_once "../includes/conexion.php";
session_start();

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_GET['publicacion_id'];
$otro_id = $_GET['otro_id'];

$stmt = $pdo->prepare("SELECT * FROM mensajes WHERE publicacion_id = ? AND ((remitente_id = ? AND destinatario_id = ?) OR (remitente_id = ? AND destinatario_id = ?)) ORDER BY fecha ASC");
$stmt->execute([$publicacion_id, $usuario_id, $otro_id, $otro_id, $usuario_id]);
$mensajes = $stmt->fetchAll();

foreach ($mensajes as $msg) {
    if ($msg['remitente_id'] == $usuario_id) {
        // Mensaje propio alineado a la derecha
        echo "<div style='text-align: right; margin: 5px;'>
                <span style='background: #dcf8c6; padding: 8px 12px; border-radius: 10px; display: inline-block;'>
                    " . htmlspecialchars($msg['mensaje']) . "
                </span>
              </div>";
    } else {
        // Mensaje del otro usuario alineado a la izquierda
        echo "<div style='text-align: left; margin: 5px;'>
                <span style='background: #ffffff; padding: 8px 12px; border-radius: 10px; display: inline-block; border: 1px solid #ccc;'>
                    " . htmlspecialchars($msg['mensaje']) . "
                </span>
              </div>";
    }
}
?>