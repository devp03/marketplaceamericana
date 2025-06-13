<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['nombre'];

// Obtener publicaciones del usuario con reputación
$stmt = $pdo->prepare("
    SELECT p.*, 
        (SELECT ROUND(AVG(c.puntuacion), 1) FROM calificaciones c WHERE c.publicacion_id = p.id) AS promedio,
        (SELECT COUNT(*) FROM calificaciones c WHERE c.publicacion_id = p.id) AS total_comentarios
    FROM publicaciones p
    WHERE p.usuario_id = ?
    ORDER BY p.fecha_publicacion DESC
");
$stmt->execute([$usuario_id]);
$publicaciones = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Marketplace</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }
        header {
            background-color: #1877f2;
            padding: 15px 30px;
            color: white;
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
        h2 {
            text-align: center;
            margin-top: 30px;
            color: #1877f2;
        }
        .publicaciones {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 30px;
            gap: 20px;
        }
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }
        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .card-body {
            padding: 15px;
        }
        .card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .card p {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
        .precio {
            font-weight: bold;
            color: #1877f2;
        }
        .estrellas {
            color: #FFD700;
            font-size: 14px;
        }
        footer {
            text-align: center;
            background-color: #e9eff8;
            padding: 20px;
            color: #333;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo-titulo">
        <img src="../img/logo.svg" alt="Logo">
        <h1>Mi Panel</h1>
    </div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="publicaciones.php">Explorar</a>
        <a href="crear_publicacion.php">Nueva publicación</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<h2>Mis publicaciones</h2>

<section class="publicaciones">
<?php foreach ($publicaciones as $p): ?>
    <a class="card" href="ver_publicacion.php?id=<?php echo $p['id']; ?>">
        <img src="../uploads/<?php echo htmlspecialchars($p['imagen']); ?>" alt="Imagen">
        <div class="card-body">
            <h3><?php echo htmlspecialchars($p['titulo']); ?></h3>
            <p class="precio">Gs <?php echo number_format($p['precio'], 0, ',', '.'); ?></p>
            <div class="estrellas">
                <?php
                    $prom = (int) $p['promedio'];
                    echo str_repeat('★', $prom) . str_repeat('☆', 5 - $prom);
                ?>
                <span style="color: #666; font-size: 12px;">
                  (<?php echo $p['total_comentarios']; ?> opiniones)
                </span>
            </div>
        </div>
    </a>
<?php endforeach; ?>
</section>

<footer>
    © 2025 Marketplace Universitario | <a href="contacto.php">Contáctanos</a>
</footer>

</body>
</html>
