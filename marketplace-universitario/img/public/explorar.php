<?php
require_once "../includes/conexion.php";
session_start();

$q = $_GET['q'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$destacada = $_GET['destacada'] ?? null;

$sql = "SELECT p.*, u.nombre AS vendedor FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (p.titulo LIKE ? OR p.descripcion LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if (!empty($categoria)) {
    $sql .= " AND p.categoria = ?";
    $params[] = $categoria;
}

$sql .= " ORDER BY p.fecha_publicacion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$publicaciones = $stmt->fetchAll();
?>

<?php include "../includes/header.php"; ?>

<h2>🏦 Publicaciones disponibles</h2>

<form method="GET" style="margin-bottom:20px; text-align:center;">
    <input type="text" name="q" placeholder="Buscar por título o descripción..." value="<?php echo htmlspecialchars($q); ?>">
    <select name="categoria">
        <option value="">Todas las categorías</option>
        <option value="Libros">Libros</option>
        <option value="Electrónica">Electrónica</option>
        <option value="Ropa">Ropa</option>
        <option value="Otros">Otros</option>
    </select>
    <button type="submit">🔍 Buscar</button>
</form>

<div style="display: flex; flex-wrap: wrap; justify-content: center;">
<?php foreach ($publicaciones as $pub): ?>
    <div class="card" id="pub-<?php echo $pub['id']; ?>" style="<?php echo ($destacada == $pub['id']) ? 'border: 2px solid #1877f2;' : ''; ?>">
        <?php if (!empty($pub['imagen'])): ?>
            <img src="../uploads/<?php echo $pub['imagen']; ?>" alt="Imagen de <?php echo htmlspecialchars($pub['titulo']); ?>">
        <?php else: ?>
            <img src="../uploads/default.png" alt="Sin imagen">
        <?php endif; ?>

        <h3><?php echo htmlspecialchars($pub['titulo']); ?></h3>
        <p><?php echo htmlspecialchars($pub['descripcion']); ?></p>
        <p>💰 Precio: Gs. <?php echo number_format($pub['precio'], 0, ',', '.'); ?></p>
        <p>📁 Categoría: <?php echo htmlspecialchars($pub['categoria']); ?></p>
        <p>👤 Vendedor: <a href="perfil.php?id=<?php echo $pub['usuario_id']; ?>"><?php echo htmlspecialchars($pub['vendedor']); ?></a></p>

        <?php
        $stmtVend = $pdo->prepare("SELECT ROUND(AVG(puntuacion),1) AS promedio, COUNT(*) AS cantidad FROM calificaciones WHERE publicacion_id = ?");
        $stmtVend->execute([$pub['id']]);
        $cal = $stmtVend->fetch();

        $promedio = $cal['promedio'] ?? 0;
        $cantidad = $cal['cantidad'] ?? 0;
        ?>

        <p>
            ⭐ <?php echo str_repeat('★', round($promedio)) . str_repeat('☆', 5 - round($promedio)); ?>
            <a href="ver_opiniones.php?id=<?php echo $pub['id']; ?>" style="font-size: 13px; color: #555;">(<?php echo $cantidad; ?> opiniones)</a>
        </p>

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <?php $yaReservadaPorMi = $pub['reservada'] && $_SESSION['usuario_id'] != $pub['usuario_id']; ?>
            <?php if (!$pub['reservada']): ?>
                <form method="POST" action="reservar.php">
                    <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                    <button type="submit" <?php echo ($pub['usuario_id'] == $_SESSION['usuario_id']) ? 'disabled title="No puedes reservar tu propia publicación"' : ''; ?>>
                        📌 Reservar
                    </button>
                </form>
            <?php elseif ($yaReservadaPorMi): ?>
                <form method="GET" action="chat.php">
                    <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                    <button type="submit">💬 Chatear con vendedor</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p><i>Inicia sesión para reservar o chatear.</i></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

<?php if ($destacada): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const pub = document.getElementById("pub-<?php echo $destacada; ?>");
        if (pub) {
            pub.scrollIntoView({ behavior: "smooth", block: "center" });
            pub.style.transition = "background-color 0.8s ease";
            pub.style.backgroundColor = "#e7f1ff";
            setTimeout(() => {
                pub.style.backgroundColor = "white";
            }, 3000);
        }
    });
</script>
<?php endif; ?>

<br><a href="dashboard.php">← Volver al panel</a>

<?php include "../includes/footer.php"; ?>
