<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['id'])) {
    header("Location: mis_publicaciones.php");
    exit;
}

$pub_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM publicaciones WHERE id = ? AND usuario_id = ?");
$stmt->execute([$pub_id, $usuario_id]);
$publicacion = $stmt->fetch();

if (!$publicacion) {
    echo "Publicación no encontrada o no tienes permiso para editarla.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $categoria = $_POST["categoria"];

    $imagen_nombre = $publicacion['imagen'];
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $imagen_nombre = time() . "_" . basename($_FILES["imagen"]["name"]);
        move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/" . $imagen_nombre);
    }

    $stmt = $pdo->prepare("UPDATE publicaciones SET titulo = ?, descripcion = ?, precio = ?, categoria = ?, imagen = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $descripcion, $precio, $categoria, $imagen_nombre, $pub_id, $usuario_id]);

    $_SESSION['mensaje_exito'] = "✅ Publicación actualizada correctamente.";
    header("Location: mis_publicaciones.php");
    exit;
}
?>

<?php include "../includes/header.php"; ?>

<main>
    <h2 style="text-align:center;">✏️ Editar publicación</h2>

    <div style="max-width: 500px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Título del producto" value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" required style="width:100%; padding:10px; margin-bottom:15px;">
            
            <textarea name="descripcion" placeholder="Descripción" required style="width:100%; padding:10px; height:100px; margin-bottom:15px;"><?php echo htmlspecialchars($publicacion['descripcion']); ?></textarea>
            
            <input type="number" name="precio" placeholder="Precio" step="0.01" value="<?php echo $publicacion['precio']; ?>" required style="width:100%; padding:10px; margin-bottom:15px;">
            
            <input type="text" name="categoria" placeholder="Categoría" value="<?php echo htmlspecialchars($publicacion['categoria']); ?>" required style="width:100%; padding:10px; margin-bottom:15px;">
            
            <?php if ($publicacion['imagen']): ?>
                <img src="../uploads/<?php echo $publicacion['imagen']; ?>" width="150" style="margin-bottom: 15px;"><br>
            <?php endif; ?>

            <input type="file" name="imagen" accept="image/*" style="margin-bottom:15px;"><br>

            <button type="submit" style="width:100%; padding:10px; background:#1877f2; color:white; border:none; border-radius:5px; font-weight:bold;">
                Actualizar publicación
            </button>
        </form>

        <br><a href="mis_publicaciones.php" style="display:block; text-align:center;">← Volver a mis publicaciones</a>
    </div>
</main>

<?php include "../includes/footer.php"; ?>
