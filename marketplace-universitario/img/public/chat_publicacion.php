<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_GET['publicacion_id'] ?? null;
$otro_id = $_GET['otro_id'] ?? null;

if (!$publicacion_id || !$otro_id) {
    echo "❌ Parámetros faltantes.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat en tiempo real</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .chat-box {
            border: 1px solid #ccc;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .mensaje {
            margin: 8px 0;
            max-width: 80%;
            padding: 8px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 0.95em;
        }
        .mensaje.enviado {
            background-color: #e7f1ff;
            align-self: flex-end;
            text-align: right;
        }
        .mensaje.recibido {
            background-color: #ffffff;
            border: 1px solid #ccc;
            text-align: left;
        }
        .chat-contenedor {
            display: flex;
            flex-direction: column;
        }
        form {
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
    </style>
    <script>
        function cargarMensajes() {
    const chatBox = document.getElementById('chat-mensajes');
    const isAtBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 20;

    fetch("ver_mensajes.php?publicacion_id=<?php echo $publicacion_id; ?>&otro_id=<?php echo $otro_id; ?>")
        .then(response => response.text())
        .then(html => {
            chatBox.innerHTML = html;
            if (!isAtBottom) {
                // no cambia el scroll si el usuario no está abajo
                return;
            }
            // baja al fondo solo si estaba al fondo antes
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

setInterval(cargarMensajes, 3000);
window.onload = cargarMensajes;

    </script>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="card" style="max-width: 600px; margin: auto;">
        <h2 style="text-align:center;">💬 Conversación en tiempo real</h2>
        <div id="chat-mensajes" class="chat-box chat-contenedor"></div>

        <form method="POST" action="">
            <input type="text" name="mensaje" placeholder="Escribe un mensaje..." required>
            <button type="submit">Enviar</button>
        </form>

        <p style="text-align:center;"><a href="chat.php">← Volver a mis chats</a></p>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = trim($_POST["mensaje"]);
    if (!empty($mensaje)) {
        $stmt = $pdo->prepare("INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->execute([$publicacion_id, $usuario_id, $otro_id, $mensaje]);
    }
    header("Location: chat_publicacion.php?publicacion_id=$publicacion_id&otro_id=$otro_id");
    exit;
}
?>
