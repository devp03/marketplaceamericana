<?php
session_start();
require_once "../includes/conexion.php";

// Verificar si el usuario tiene acceso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario || $usuario['correo'] !== 'matias@alumno.ua.edu.py') {
    echo "❌ No tienes permiso para acceder a esta página.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM alumno_mmartinez WHERE correo = ?");
$stmt->execute([$usuario['correo']]);
$alumno = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acerca de mí - Matias Martinez</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #eef2f7;
            color: #333;
        }
        header {
            background-color:rgb(122, 26, 232);
            color: white;
            text-align: center;
            padding: 20px;
        }
        main {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        img.perfil {
            max-width: 200px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info {
            font-size: 16px;
            margin-bottom: 30px;
        }
        #gato {
            width: 100px;
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
<header>
    <h1>Acerca de mí</h1>
    <p>Matias Martinez</p>
</header>
<main>
    <img src="../img/matias_perfil.jpg" alt="Foto de Matias Martinez" class="perfil">
    <div class="info">
        <?php if ($alumno): ?>
            <p><strong>Nombre completo:</strong> <?php echo htmlspecialchars($alumno['nombres']); ?></p>
            <p><strong>Cédula:</strong> <?php echo htmlspecialchars($alumno['ci']); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($alumno['correo']); ?></p>
        <?php else: ?>
            <p>No se encontraron datos en la tabla <code>alumno_mmartinez</code>.</p>
        <?php endif; ?>
    </div>

    <h3>¡Mirá cómo baila el gato!</h3>
    <img src="../img/gato.gif" id="gato" alt="Gato bailando">
    <br>
    <button onclick="bailar()">Hazlo bailar 🎶</button>
</main>

<script>
function bailar() {
    const gato = document.getElementById('gato');
    gato.style.transform = 'rotate(20deg) scale(1.2)';
    setTimeout(() => {
        gato.style.transform = 'rotate(-20deg) scale(1.2)';
        setTimeout(() => {
            gato.style.transform = 'rotate(0deg) scale(1)';
        }, 300);
    }, 300);
}
</script>
</body>
</html>
