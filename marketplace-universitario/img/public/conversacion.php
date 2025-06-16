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

// Verificar que exista la publicación
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
        header("Location: conversacion.php?pub_id=$publicacion_id&con=$otro_usuario_id");
        exit;
    }
}

// Obtener mensajes
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
    <link rel="stylesheet" href="estilo.css">
    <style>
        .chat-box {
            border: 1px solid #ccc;
            padding: 12px;
            height: 300px;
            overflow-y: auto;
            background-color: #f1f1f1;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .mensaje {
            margin: 8px 0;
            max-width: 75%;
            padding: 8px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 0.95em;
            clear: both;
        }
        .mio {
            background-color: #e7f1ff;
            float: right;
            text-align: right;
        }
        .otro {
            background-color: #ffffff;
            border: 1px solid #ccc;
            float: left;
            text-align: left;
        }
        .chat-container {
            max-width: 600px;
            margin: auto;
        }
    </style>
    <script>
        function mantenerScroll() {
            const box = document.querySelector('.chat-box');
            const isAtBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 10;

            fetch("ver_mensajes.php?publicacion_id=<?= $publicacion_id; ?>&otro_id=<?= $otro_usuario_id; ?>")
                .then(response => response.text())
                .then(html => {
                    box.innerHTML = html;
                    if (isAtBottom) {
                        box.scrollTop = box.scrollHeight;
                    }
                });
        }

        setInterval(mantenerScroll, 3000);
        window.onload = mantenerScroll;
    </script>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="chat-container">
        <h2 style="text-align: center;">💬 Chat sobre: <?= htmlspecialchars($pub['titulo']); ?></h2>

        <div class="chat-box" id="chat-box">
            <?php foreach ($mensajes as $msg): ?>
                <div class="mensaje <?= $msg['emisor_id'] == $usuario_id ? 'mio' : 'otro'; ?>">
                    <strong><?= htmlspecialchars($msg['emisor']); ?>:</strong><br>
                    <?= htmlspecialchars($msg['mensaje']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="form-chat">
            <input type="text" name="mensaje" placeholder="Escribe tu mensaje..." required>
            <button type="submit">Enviar</button>
        </form>

        <p style="text-align:center;"><a href="chat.php">← Volver a chats</a></p>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>
