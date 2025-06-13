<?php
require_once '../includes/conexion.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Publicación no especificada.";
    exit;
}

$id = (int) $_GET['id'];

// Obtener datos de la publicación
$stmt = $pdo->prepare("SELECT p.*, u.nombre AS autor FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$publicacion) {
    echo "Publicación no encontrada.";
    exit;
}

// Obtener opiniones
$stmtOpiniones = $pdo->prepare("
    SELECT c.puntuacion, c.comentario, c.fecha, u.nombre AS calificador
    FROM calificaciones c
    JOIN usuarios u ON c.calificador_id = u.id
    WHERE c.publicacion_id = ?
    ORDER BY c.fecha DESC
");
$stmtOpiniones->execute([$id]);
$opiniones = $stmtOpiniones->fetchAll();

// Verificar si el usuario ya opinó
$yaOpino = false;
if (isset($_SESSION['usuario_id'])) {
    $stmtCheck = $pdo->prepare("SELECT * FROM calificaciones WHERE publicacion_id = ? AND calificador_id = ?");
    $stmtCheck->execute([$id, $_SESSION['usuario_id']]);
    $yaOpino = $stmtCheck->rowCount() > 0;
}

// Guardar nueva opinión
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['usuario_id']) && !$yaOpino) {
    $puntuacion = (int) $_POST["puntuacion"];
    $comentario = trim($_POST["comentario"]);
    $calificador = $_SESSION["usuario_id"];
    $evaluado = $publicacion["usuario_id"];

    $stmtInsert = $pdo->prepare("INSERT INTO calificaciones (publicacion_id, calificador_id, evaluado_id, puntuacion, comentario, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtInsert->execute([$id, $calificador, $evaluado, $puntuacion, $comentario]);

    header("Location: ver_publicacion.php?id=" . $id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($publicacion['titulo']); ?></title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        header {
            background-color: #1877f2;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-titulo {
            display: flex;
            align-items: center;
        }

        .logo-titulo img {
            height: 40px;
            margin-right: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .contenedor {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .imagen {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .estrellas {
            color: #FFD700;
            font-size: 16px;
        }

        .opiniones {
            margin-top: 40px;
        }

        .opinion {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            resize: vertical;
            border: 1px solid #ccc;
        }

        form select {
            padding: 8px;
            margin-top: 10px;
        }

        form button {
            margin-top: 15px;
            background-color: #1877f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        footer {
            text-align: center;
            background-color: #e9eff8;
            padding: 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo-titulo">
        <img src="../img/logo.svg" alt="Logo">
        <h1>Marketplace Universitario</h1>
    </div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="publicaciones.php">Publicaciones</a>
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php">Ingresar</a>
            <a href="registro.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>

<div class="contenedor">
    <img src="../uploads/<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen" class="imagen">
    <h2><?php echo htmlspecialchars($publicacion['titulo']); ?></h2>
    <p><strong>Precio:</strong> Gs <?php echo number_format($publicacion['precio'], 0, ',', '.'); ?></p>
    <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($publicacion['descripcion'])); ?></p>
    <p><strong>Publicado por:</strong> <?php echo htmlspecialchars($publicacion['autor']); ?></p>

    <div class="opiniones">
        <h3>Opiniones de usuarios</h3>
        <?php if (count($opiniones) > 0): ?>
            <?php foreach ($opiniones as $op): ?>
                <div class="opinion">
                    <strong><?php echo htmlspecialchars($op['calificador']); ?></strong>
                    <span style="color: #888; font-size: 12px;">(<?php echo date("d/m/Y H:i", strtotime($op['fecha'])); ?>)</span>
                    <div class="estrellas">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $op['puntuacion'] ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($op['comentario'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #777;">Aún no hay opiniones.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['usuario_id']) && !$yaOpino && $_SESSION['usuario_id'] != $publicacion['usuario_id']): ?>
        <hr>
        <h3>Dejar tu opinión</h3>
        <form method="POST">
            <label for="puntuacion">Puntuación:</label><br>
            <select name="puntuacion" id="puntuacion" required>
                <option value="">-- Seleccionar --</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>"><?= $i ?> estrella<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select><br><br>

            <label for="comentario">Comentario:</label><br>
            <textarea name="comentario" id="comentario" rows="4" required></textarea><br>

            <button type="submit">Enviar opinión</button>
        </form>
    <?php elseif ($yaOpino): ?>
        <p style="margin-top: 20px; color: green;">✅ Ya dejaste una opinión para esta publicación.</p>
    <?php endif; ?>
</div>

<footer>
    © 2025 Marketplace Universitario | <a href="contacto.php">Contáctanos</a>
</footer>

</body>
</html>

