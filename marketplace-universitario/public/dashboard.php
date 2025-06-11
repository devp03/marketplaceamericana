<?php
require_once "../includes/conexion.php";
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION["nombre"];
$stmt = $pdo->prepare("SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE evaluado_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$reputacion = $stmt->fetch();
$promedio = round($reputacion['promedio'], 2);
$total = $reputacion['total'];

// Variables para buscar publicaciones
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
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="estilo.css">
    <script>
        fetch("verificar_mensajes.php")
          .then(res => res.json())
          .then(data => {
            if (data.nuevos > 0) {
              document.getElementById("notif").style.display = "inline";
            }
          });
    </script>
</head>
<body>
<header class="cabecera">
    <div class="cabecera-contenido">
        <span class="bienvenida">👋 Bienvenido, <?php echo htmlspecialchars($nombre); ?></span>

        <div class="dropdown">
            <button class="dropbtn" id="menuBtn">☰ Opciones</button>
            <div class="dropdown-content" id="menuContent">
                <a href="crear_publicacion.php">➕ Crear publicación</a>
                <a href="mis_publicaciones.php">📦 Mis publicaciones</a>
                <a href="explorar.php">📌 Explorar</a>
                <a href="chat.php">💬 Mensajes <span id="notif" style="color:red; display:none;">●</span></a>
                <a href="logout.php">🚪 Salir</a>
            </div>
        </div>
    </div>
</header>

<!-- <p class="reputacion">🌟 Reputación: 
    <?php echo $total > 0 ? "$promedio/5 ($total opiniones)" : "Sin calificaciones aún"; ?>
</p> -->

<main>
    <h2>🛍️ Publicaciones disponibles</h2>

    <?php if(count($publicaciones) === 0): ?>
        <p>No se encontraron publicaciones.</p>
    <?php else: ?>
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
                    <?php
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
                    <p><i>Inicia sesión para reservar.</i></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script>
  document.getElementById("menuBtn").addEventListener("click", function(event) {
    event.stopPropagation();
    const menu = document.getElementById("menuContent");
    if (menu.style.display === "block") {
      menu.style.display = "none";
    } else {
      menu.style.display = "block";
    }
  });

  window.addEventListener("click", function() {
    document.getElementById("menuContent").style.display = "none";
  });
</script>

<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #f4f4f4;
  }

  .cabecera {
      background-color: #007BFF;
      color: white;
      padding: 10px 20px;
      width: 100%;
      box-sizing: border-box;
  }

  .cabecera-contenido {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
  }

  .bienvenida {
      font-size: 1em;
  }

  /* Menú desplegable */
  .dropdown {
      position: relative;
  }

  .dropbtn {
      background-color: #0056b3;
      color: white;
      padding: 8px 12px;
      font-size: 14px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
  }

  .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 200px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
  }

  .dropdown-content a {
      color: black;
      padding: 10px 15px;
      text-decoration: none;
      display: block;
  }

  .dropdown-content a:hover {
      background-color: #f1f1f1;
  }

  /* Eliminado el hover que mostraba el menú */
  /*
  .dropdown:hover .dropdown-content {
      display: block;
  }
  */

  .reputacion {
      margin: 20px;
      font-size: 1.1em;
  }

  main {
    padding: 20px;
  }
</style>
</body>
</html>
