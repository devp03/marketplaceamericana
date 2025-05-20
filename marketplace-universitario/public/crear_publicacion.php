<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $categoria = $_POST["categoria"];
    $usuario_id = $_SESSION["usuario_id"];

    // Guardar imagen
    $imagen_nombre = "";
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $imagen_nombre = time() . "_" . basename($_FILES["imagen"]["name"]);
        $ruta = "../uploads/" . $imagen_nombre;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
    }

    $stmt = $pdo->prepare("INSERT INTO publicaciones (titulo, descripcion, precio, imagen, categoria, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $precio, $imagen_nombre, $categoria, $usuario_id]);

    $mensaje = "✅ Publicación creada exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilo.css">
    <meta charset="UTF-8">
    <title>Crear publicación</title>
</head>
<body>
    <h2>Nueva Publicación</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título del producto" required><br><br>
        <textarea name="descripcion" placeholder="Descripción" required></textarea><br><br>
        <input type="number" name="precio" placeholder="Precio" step="0.01" required><br><br>
        <input type="text" name="categoria" placeholder="Categoría" required><br><br>
        <input type="file" name="imagen" accept="image/*"><br><br>
        <button type="submit">Publicar</button>
    </form>
    <p><?php echo $mensaje; ?></p>
    <a href="dashboard.php">← Volver al panel</a>
</body>
</html>
