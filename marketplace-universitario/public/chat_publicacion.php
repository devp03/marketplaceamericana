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
    <script>
        // Función para cargar mensajes cada 3 segundos
        function cargarMensajes() {
            fetch(ver_mensajes.php?publicacion_id=<?php echo $publicacion_id; ?>&otro_id=<?php echo $otro_id; ?>)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('chat-mensajes').innerHTML = html;
                });
        }

        setInterval(cargarMensajes, 3000); // cada 3 segundos
        window.onload = cargarMensajes; // cargar al iniciar
    </script>
</head>
<body>
    <h2>💬 Conversación en tiempo real</h2>
    <div id="chat-mensajes"></div>

    <form method="POST" action="">
        <input type="text" name="mensaje" placeholder="Escribe un mensaje..." required>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="chat.php">← Volver a mis chats</a></p>
</body>
</html>

<?php
// Enviar mensaje si se recibe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = trim($_POST["mensaje"]);
    if (!empty($mensaje)) {
        $stmt = $pdo->prepare("INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->execute([$publicacion_id, $usuario_id, $otro_id, $mensaje]);
    }
    // Redirigir para limpiar el POST y evitar reenvíos
    header("Location: chat_publicacion.php?publicacion_id=$publicacion_id&otro_id=$otro_id");
    exit;
}
?>