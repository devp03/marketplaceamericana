<?php
require_once "../includes/conexion.php";
session_start();
include '../includes/header.php';
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Chats donde soy vendedor
$stmt_vendedor = $pdo->prepare("
    SELECT DISTINCT p.id AS publicacion_id, p.titulo, u.id AS otro_id, u.nombre 
    FROM publicaciones p
    JOIN reservas r ON r.publicacion_id = p.id
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE p.usuario_id = ?
");
$stmt_vendedor->execute([$usuario_id]);
$vendedorChats = $stmt_vendedor->fetchAll();

// Chats donde soy comprador
$stmt_comprador = $pdo->prepare("
    SELECT DISTINCT p.id AS publicacion_id, p.titulo, u.id AS otro_id, u.nombre 
    FROM reservas r
    JOIN publicaciones p ON r.publicacion_id = p.id
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE r.usuario_id = ?
");
$stmt_comprador->execute([$usuario_id]);
$compradorChats = $stmt_comprador->fetchAll();

$publicacion_id = $_GET['publicacion_id'] ?? null;
$otro_id = $_GET['otro_id'] ?? null;
$mensajes = [];

if ($publicacion_id && $otro_id) {
    $stmt2 = $pdo->prepare("SELECT * FROM mensajes WHERE publicacion_id = ? AND ((remitente_id = ? AND destinatario_id = ?) OR (remitente_id = ? AND destinatario_id = ?)) ORDER BY fecha ASC");
    $stmt2->execute([$publicacion_id, $usuario_id, $otro_id, $otro_id, $usuario_id]);
    $mensajes = $stmt2->fetchAll();

    $marcarLeidos = $pdo->prepare("UPDATE mensajes SET leido = 1 WHERE publicacion_id = ? AND destinatario_id = ? AND leido = 0");
    $marcarLeidos->execute([$publicacion_id, $usuario_id]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Chats</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        <?php include "../includes/chat_estilo.css"; ?>
    </style>
    <script>
        function autoRefresh() {
            const input = document.querySelector('input[name="mensaje"]');
            if (input && document.activeElement !== input) {
                window.location.reload();
            }
        }
        setInterval(autoRefresh, 3000);
        window.onload = () => {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        };
    </script>
</head>
<body>
<main>
    <h2>📨 Mis Conversaciones</h2>

    <h3>📦 Publicaciones en venta</h3>
    <?php foreach ($vendedorChats as $c): ?>
        <?php
        $stmtNotif = $pdo->prepare("SELECT COUNT(*) FROM mensajes WHERE publicacion_id = ? AND remitente_id = ? AND destinatario_id = ? AND leido = 0");
        $stmtNotif->execute([$c['publicacion_id'], $c['otro_id'], $usuario_id]);
        $notificacion = $stmtNotif->fetchColumn();
        $stmtCal = $pdo->prepare("SELECT ROUND(AVG(puntuacion),1) FROM calificaciones WHERE evaluado_id = ?");
        $stmtCal->execute([$c['otro_id']]);
        $calif = $stmtCal->fetchColumn();
        ?>
        <div class="chat-link">
            <a href="?publicacion_id=<?php echo $c['publicacion_id']; ?>&otro_id=<?php echo $c['otro_id']; ?>">
                💬 <?php echo htmlspecialchars($c['titulo']) . " con " . htmlspecialchars($c['nombre']); ?>
                <span>⭐ <?php echo $calif ?: 'Sin calificación'; ?></span>
                <?php if ($notificacion > 0): ?>
                    <span class="badge"><?php echo $notificacion; ?></span>
                <?php endif; ?>
            </a>
        </div>
    <?php endforeach; ?>

    <h3>🛒 Publicaciones de interés</h3>
    <?php foreach ($compradorChats as $c): ?>
        <?php
        $stmtNotif = $pdo->prepare("SELECT COUNT(*) FROM mensajes WHERE publicacion_id = ? AND remitente_id = ? AND destinatario_id = ? AND leido = 0");
        $stmtNotif->execute([$c['publicacion_id'], $c['otro_id'], $usuario_id]);
        $notificacion = $stmtNotif->fetchColumn();
        $stmtCal = $pdo->prepare("SELECT ROUND(AVG(puntuacion),1) FROM calificaciones WHERE evaluado_id = ?");
        $stmtCal->execute([$c['otro_id']]);
        $calif = $stmtCal->fetchColumn();
        ?>
        <div class="chat-link">
            <a href="?publicacion_id=<?php echo $c['publicacion_id']; ?>&otro_id=<?php echo $c['otro_id']; ?>">
                💬 <?php echo htmlspecialchars($c['titulo']) . " con " . htmlspecialchars($c['nombre']); ?>
                <span>⭐ <?php echo $calif ?: 'Sin calificación'; ?></span>
                <?php if ($notificacion > 0): ?>
                    <span class="badge"><?php echo $notificacion; ?></span>
                <?php endif; ?>
            </a>
        </div>
    <?php endforeach; ?>

    <?php if ($publicacion_id && $otro_id): ?>
        <h3>💬 Conversación</h3>
        <div class="chat-box" id="chat-box">
            <?php foreach ($mensajes as $msg): ?>
                <div class="mensaje <?php echo $msg['remitente_id'] == $usuario_id ? 'mio' : 'otro'; ?>">
                    <?php echo htmlspecialchars($msg['mensaje']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="enviar_mensaje.php" class="form-chat">
            <input type="hidden" name="publicacion_id" value="<?php echo $publicacion_id; ?>">
            <input type="hidden" name="otro_id" value="<?php echo $otro_id; ?>">
            <input type="text" name="mensaje" placeholder="Escribe un mensaje..." required>
            <button type="submit">Enviar</button>
        </form>

        <form method="POST" action="terminar_chat.php" style="margin-top:10px;">
            <input type="hidden" name="publicacion_id" value="<?php echo $publicacion_id; ?>">
            <input type="hidden" name="otro_id" value="<?php echo $otro_id; ?>">
            <button type="submit" style="background:#e74c3c; color:white;">🛑 Terminar Chat</button>
        </form>
    <?php endif; ?>

    <hr>
    <a href="dashboard.php"><button>🏠 Volver al Panel Principal</button></a>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
