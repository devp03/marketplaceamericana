<?php
session_start();

// Solo admin puede eliminar usuarios
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "../includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = (int) $_POST["id"];

    // Evitar que admin se borre a sí mismo (opcional)
    // if ($id === $_SESSION["admin_id"]) {
    //     header("Location: usuarios.php?error=no_se_puede_eliminar");
    //     exit;
    // }

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: usuarios.php");
exit;
