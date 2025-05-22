<?php
session_start();



require_once "../includes/conexion.php";

// Consultar todos los usuarios
$stmt = $pdo->prepare("SELECT id, nombre, correo FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Usuarios - Admin</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 30px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #1877f2;
            color: white;
        }
        tr:hover {background-color: #f1f1f1;}
        .btn-eliminar {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-eliminar:hover {
            background-color: #c9302c;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #1877f2;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Usuarios</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nombre']) ?></td>
                    <td><?= htmlspecialchars($user['correo']) ?></td>
                    <td>
                        <form method="POST" action="eliminar_registro.php" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="4" style="text-align:center;">No hay usuarios registrados.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
