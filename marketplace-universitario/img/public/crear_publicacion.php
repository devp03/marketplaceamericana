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
        // Validar tamaño (máximo 2MB)
        if ($_FILES["imagen"]["size"] <= 2 * 1024 * 1024) {
            $imagen_nombre = time() . "_" . basename($_FILES["imagen"]["name"]);
            $ruta = "../uploads/" . $imagen_nombre;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
        } else {
            $mensaje = "❌ La imagen supera el tamaño máximo de 2MB.";
        }
    }

    if ($mensaje === "") {
        $stmt = $pdo->prepare("INSERT INTO publicaciones (titulo, descripcion, precio, imagen, categoria, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $precio, $imagen_nombre, $categoria, $usuario_id]);
        $_SESSION['mensaje_exito'] = "✅ Publicación creada exitosamente.";
        header("Location: dashboard.php");
        exit;

    }
}
?>

<?php include "../includes/header.php"; ?>

<h2>Nueva Publicación</h2>

<div class="formulario">
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título del producto" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" name="precio" placeholder="Precio" step="0.01" required>
        <input type="text" name="categoria" placeholder="Categoría" required>
        
        <input type="file" name="imagen" accept="image/*" onchange="mostrarVistaPrevia(this)">
        <div id="contenedor-vista" style="margin-top: 10px; display: none;">
            <img id="vista-previa" src="" alt="Vista previa" style="max-width: 100%; border-radius: 8px; margin-bottom: 10px;">
            <button type="button" onclick="quitarImagen()" style="background: #ccc; color: #333; border: none; padding: 6px 10px; border-radius: 5px; cursor: pointer;">❌ Quitar imagen</button>
        </div>

        <button type="submit">Publicar</button>
    </form>

    <p><?php echo $mensaje; ?></p>
    <p><a href="dashboard.php">← Volver al panel</a></p>
</div>

<script>
function mostrarVistaPrevia(input) {
    const file = input.files[0];
    const vista = document.getElementById("vista-previa");
    const contenedor = document.getElementById("contenedor-vista");

    if (file) {
        const lector = new FileReader();
        lector.onload = function(e) {
            vista.src = e.target.result;
            contenedor.style.display = "block";
        };
        lector.readAsDataURL(file);
    } else {
        quitarImagen();
    }
}

function quitarImagen() {
    document.querySelector('input[name="imagen"]').value = "";
    document.getElementById("vista-previa").src = "";
    document.getElementById("contenedor-vista").style.display = "none";
}
</script>

<?php include "../includes/footer.php"; ?>
