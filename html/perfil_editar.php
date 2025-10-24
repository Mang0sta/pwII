<?php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'Database.php';
$database = new Database();
$conn = $database->getConnection();

$email = $_SESSION['email'];

// recuperar la información del usuario
$stmt = $conn->prepare("SELECT nombre, apellidos, genero, cumpleanos, fotoRuta, fotoNombre FROM a WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $name = $row['nombre'];
    $apellidos = $row['apellidos'];
    $genero = $row['genero'];
    $cumpleanos = $row['cumpleanos'];
    $fotoRuta = $row['fotoRuta'];
    $fotoNombre = $row['fotoNombre'];
} else {
    // 
    echo "Error: No se encontró la información del usuario.";
    exit();
}

$stmt->close();
$database->closeConnection();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/perfil_editar.css">
    <link rel="stylesheet" type="text/css" href="../css/perfil_editar_dark.css" class="dark-mode-style" disabled>
</head>

<body style="position: relative;">
<header class="main-header">
  <a href="publicacion.php" class="logo">⚽ Stadium</a>
  <div class="nav-buttons">
  
    <?php if (isset($_SESSION['email'])): ?>
      <button id="darkModeToggle" class="dark-toggle">Modo Oscuro</button>
      <a href="publicacion.php" class="nav-btn">Inicio</a>
      <a href="perfil_usuario.php?user_id=<?= $_SESSION['user_id_mysql'] ?>" class="nav-btn">Perfil</a>
      <a href="perfil_editar.php" class="nav-btn">Perfil editar</a>
      
     
        <a href="reportes.php" class="nav-btn">Reportes</a>
      
      <a href="logout.php" class="nav-btn">Cerrar Sesión</a>
    <?php endif; ?>
  </div>
</header>
    

    <div class="container">
    <div class="form-box active" id="editar-form">
        <form method="POST" action="user_modificar.php" enctype="multipart/form-data">
            <h2>Editar información de usuario</h2>
            <input type="text" name="name" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Nombre"  pattern="[^\s0-9()[\]{}*&^%$#@_!´/=?¿¡'|¨+,;.:-][^0-9()[\]{}*&^%$#@_!´/=?¿¡'|¨+,;.:-]{1,30}$" required value="<?= $name ?>">
            <input type="text" name="apellidos" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Apellidos" required value="<?= $apellidos ?>">

            <select name="genero" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" required>
                <option value="">--Género--</option>
                <option value="hombre" <?= ($genero === 'hombre') ? 'selected' : '' ?>>Hombre</option>
                <option value="mujer" <?= ($genero === 'mujer') ? 'selected' : '' ?>>Mujer</option>
            </select>
            <input type="email" name="email" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Email"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$" required value="<?= $email ?>" readonly>
            <input type="password" name="password" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Nueva Contraseña (opcional)"  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,40}$">
            <input type="date" class="form-control" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" id="ID_FECHA_REGRISTRARSE.HTML"  name="Fecha_Nacimiento"   required value="<?= $cumpleanos ?>">

            <?php if ($fotoRuta): ?>
                <div>
                    <img src="<?= $fotoRuta ?>" alt="Foto de perfil actual" style="max-width: 100px; border-radius: 5px;">
                   
                </div>
            <?php else: ?>
                <p>No hay foto de perfil actual.</p>
            <?php endif; ?>

            <label for="ID_FOTO_REGISTRARSE.HTML">Subir nueva foto de usuario:</label>
            <input type="file" class="form-control" id="ID_FOTO_REGISTRARSE.HTML" name="fotouser" > 
            <button type="submit" name="editar" >Guardar Cambios</button>
            <button type="button" name="cancelar" onclick="window.location = 'publicacion.php'"  >Cancelar</button>
            <button type="button" class="delete-account-button" onclick="confirmDeleteAccount()">Borrar Cuenta</button>
        </form>
    </div>
</div>
    
    <script src="script.js"></script>

    <script>
        var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; 
    var yyyy = today.getFullYear();
    if(dd<10){
    dd='0'+dd
    } 
    if(mm<10){
    mm='0'+mm
    } today = yyyy + '-' + mm + '-' + dd;
    document.getElementById("ID_FECHA_REGRISTRARSE.HTML").setAttribute("max", today);

    </script>

<script>
    function handleImageUpload() 
    {

    var image = document.getElementById("ID_FOTO_REGISTRARSE.HTML").files[0];

        var reader = new FileReader();

        reader.onload = function(e) {
        document.getElementById("display-image").src = e.target.result;
        }

        reader.readAsDataURL(image);

} 


</script>

<script>
function confirmDeleteAccount() {
    if (confirm("¿Estás seguro de que deseas borrar tu cuenta? Esta acción eliminará tu perfil, todas tus publicaciones y comentarios de forma permanente.")) {
        window.location.href = 'eliminar_usuario.php';
    }
}



//modo oscuro funcion
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeStyle = document.querySelector('.dark-mode-style');

    // Comprobar si el modo oscuro estaba activado en la visita anterior
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
    if (isDarkMode) {
        darkModeStyle.disabled = false;
        darkModeToggle.textContent = 'Desactivar Modo Oscuro';
    }

    darkModeToggle.addEventListener('click', function() {
        darkModeStyle.disabled = !darkModeStyle.disabled;
        const isCurrentlyDark = !darkModeStyle.disabled;
        localStorage.setItem('darkMode', isCurrentlyDark ? 'enabled' : 'disabled');
        this.textContent = isCurrentlyDark ? 'Desactivar Modo Oscuro' : 'Activar Modo Oscuro';
    });
});

</script>

</body>
</html>