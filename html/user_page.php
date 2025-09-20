<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'];
$apellidos = $_SESSION['apellidos'];
$genero = $_SESSION['genero'];
$email = $_SESSION['email'];
$cumpleanos = $_SESSION['cumpleanos'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User pagina</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>

    <div class="box">
        <h1> Bienvenido, <span> <?= $name ?>   </span></h1>
        <p>Estas en la  <span> pagina de </span> usuarios </p>
        <p><strong>Nombre:</strong> <?= $name ?></p>
        <p><strong>Apellidos:</strong> <?= $apellidos ?></p>
        <p><strong>Género:</strong> <?= $genero ?></p>
        <p><strong>Email:</strong> <?= $email ?></p>
        <p><strong>Cumpleaños:</strong> <?= $cumpleanos ?></p>
        <button onclick="window.location.href='publicacion.php'">publicacion</button>
        <button onclick="window.location.href='logout.php'">Logout</button>
        <button onclick="window.location.href='perfil_editar.php'">editar</button>
        <button onclick="window.location.href='perfil2.php'">perfil</button>
        <button onclick="window.location.href='chat.php'">chat</button>


    </div>

</body>
</html>