<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio - Marketplace Universitario</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      color: #333;
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

    .contenedor {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      color: #1877f2;
      border-bottom: 2px solid #1877f2;
      padding-bottom: 5px;
    }

    ul {
      padding-left: 20px;
    }

    footer {
      text-align: center;
      background-color: #e9eff8;
      padding: 20px;
      color: #333;
      margin-top: 50px;
    }

    footer a {
      color: #1877f2;
      text-decoration: none;
      font-weight: bold;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <header>
    <div class="logo-titulo">
      <img src="../img/logo.svg" alt="Logo del proyecto">
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
    <h2>Integrantes del grupo</h2>
    <ul>
      <li>Pietro Urdapilleta</li>
      <li>Araceli Villalba</li>
      <li>Matias Martinez</li>
	  <li>Victor Benitez</li>
      <li>Lucas Espinola</li>
    </ul>

    <h2>Descripción general del proyecto</h2>
    <p>
      El proyecto consiste en el desarrollo de una plataforma web tipo <strong>Marketplace Universitario</strong>,
      que permite a los estudiantes publicar, vender o intercambiar productos dentro de su comunidad educativa.
      El objetivo es fomentar el comercio interno de artículos como libros, tecnología, ropa y servicios estudiantiles, de forma segura, rápida y sencilla.
    </p>
    <p>
      La interfaz está diseñada con una estética limpia y accesible, empleando una paleta de colores basada en
      <strong>tonos azules y blancos</strong>, que reflejan seriedad y confianza. La tipografía utilizada será sans-serif
      para asegurar legibilidad, y se incluirá un logotipo institucional o representativo del entorno universitario.
    </p>

    <h2>Funcionalidades destacadas</h2>
    <ul>
      <li>
        <strong>Registro e inicio de sesión de usuarios:</strong> el sistema permite que cada estudiante cree su cuenta
        personal mediante un formulario de registro. Luego podrá iniciar sesión para acceder a funciones como publicar,
        reservar productos, editar su perfil y comunicarse con otros usuarios. Se realiza validación de credenciales y
        control de sesión segura.
      </li>
      <li>
        <strong>Gestión de publicaciones:</strong> los usuarios autenticados pueden crear publicaciones detalladas de productos,
        subir imágenes, asignar precios y categorías. Todo se almacena en la base de datos y puede ser editado o eliminado por su autor.
      </li>
      <li>
        <strong>Reservas de productos y mensajería interna:</strong> otros estudiantes pueden reservar productos publicados,
        y contactar directamente al vendedor mediante un sistema de mensajes privados dentro del sitio.
        Esto fomenta el contacto directo y seguro dentro de la plataforma.
      </li>
    </ul>
  </div>

  <footer>
    © 2025 Marketplace Universitario - Desarrollado por el grupo 3 |
    <a href="https://unimarketua.blogspot.com/2025/05/un-marketplace-universitario.html">Contáctenos</a>
  </footer>

</body>
</html>
