<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$publicacion_id = $_GET['pub_id'] ?? null;
$otro_usuario_id = $_GET['con'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$publicacion_id || !$otro_usuario_id) {
    echo "❌ Conversación inválida.";
    exit;
}

// Verificamos que el usuario tenga relación con esta publicación
$stmt = $pdo->prepare("SELECT * FROM publicaciones WHERE id = ?");
$stmt->execute([$publicacion_id]);
$pub = $stmt->fetch();

if (!$pub) {
    echo "❌ Publicación no encontrada.";
    exit;
}

// Enviar mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = trim($_POST["mensaje"]);
    if (!empty($mensaje)) {
        $stmt = $pdo->prepare("INSERT INTO mensajes (publicacion_id, emisor_id, receptor_id, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->execute([$publicacion_id, $usuario_id, $otro_usuario_id, $mensaje]);
    }
}

// Obtener historial de mensajes entre ambos usuarios para esta publicación
$stmt = $pdo->prepare("SELECT m.*, u.nombre AS emisor FROM mensajes m
                       JOIN usuarios u ON m.emisor_id = u.id
                       WHERE m.publicacion_id = ?
                       AND ((m.emisor_id = ? AND m.receptor_id = ?) OR (m.emisor_id = ? AND m.receptor_id = ?))
                       ORDER BY m.fecha ASC");
$stmt->execute([$publicacion_id, $usuario_id, $otro_usuario_id, $otro_usuario_id, $usuario_id]);
$mensajes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            background-color: #f9f9f9;
        }
        .mensaje {
            margin-bottom: 10px;
        }
        .emisor {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>💬 Chat sobre: <?= htmlspecialchars($pub['titulo']); ?></h2>

    <div class="chat-box">
        <?php foreach ($mensajes as $msg): ?>
            <div class="mensaje">
                <span class="emisor"><?= htmlspecialchars($msg['emisor']); ?>:</span>
                <?= htmlspecialchars($msg['mensaje']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <textarea name="mensaje" placeholder="Escribe tu mensaje..." required style="width:100%; height:60px;"></textarea><br>
        <button type="submit">Enviar</button>
    </form>

    <br><a href="chat.php">← Volver a chats</a>
</body>
</html>
