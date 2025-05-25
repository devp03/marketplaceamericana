<?php
require_once "../includes/conexion.php";
session_start();

$q = $_GET['q'] ?? '';
$categoria = $_GET['categoria'] ?? '';

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

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8">
    <title>Explorar productos</title>
</head>

<body>
    <form method="GET" style="margin-bottom:20px;">
        <input type="text" name="q" placeholder="Buscar por título o descripción..."
            value="<?php echo $_GET['q'] ?? ''; ?>">
        <select name="categoria">
            <option value="">Todas las categorías</option>
            <option value="Libros">Libros</option>
            <option value="Electrónica">Electrónica</option>
            <option value="Ropa">Ropa</option>
            <option value="Otros">Otros</option>
        </select>
        <button type="submit">🔍 Buscar</button>
    </form>

    <h2>🛍️ Publicaciones disponibles</h2>

    <?php foreach ($publicaciones as $pub): ?>
        <div style="border:1px solid #ccc; margin:10px; padding:10px;">
            <h3><?php echo htmlspecialchars($pub['titulo']); ?></h3>
            <p><?php echo htmlspecialchars($pub['descripcion']); ?></p>
            <p>💰 Precio: Gs. <?php echo number_format($pub['precio'], 0, ',', '.'); ?></p>
            <p>📁 Categoría: <?php echo htmlspecialchars($pub['categoria']); ?></p>
            <p>Vendedor: <a href="perfil.php?id=<?php echo $pub['usuario_id']; ?>"><?php echo htmlspecialchars($pub['vendedor']); ?></a></p>
            <?php if (!empty($pub['imagen'])): ?>
                <img src="../uploads/<?php echo $pub['imagen']; ?>" alt="Imagen" width="150"><br>
            <?php endif; ?>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php
                    // Verificamos si el usuario ya reservó esta publicación
                    $yaReservadaPorMi = $pub['reservada'] && $_SESSION['usuario_id'] != $pub['usuario_id'];
                    ?>
                    <?php if (!$pub['reservada']): ?>
                        <form method="POST" action="reservar.php">
                            <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                            <button type="submit">📌 Reservar</button>
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
            <?php else: ?>
                <p><i>Inicia sesión para reservar.</i></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <br><a href="dashboard.php">← Volver al panel</a>
</body>

</html>