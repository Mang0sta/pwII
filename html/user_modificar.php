<?php

session_start();
require_once 'Database.php';
$database = new Database();
$conn = $database->getConnection();

// 
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 
$uploadDir = 'uploads/profile_pics/';

// 
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_POST['editar'])) {
    error_log('Formulario editar enviado'); // 
    $name = $_POST['name'];
    $apellidos = $_POST['apellidos'];
    $genero = $_POST['genero'];
    $email = $_POST['email']; // 
    $cumpleanos = $_POST['Fecha_Nacimiento'];
    $password = $_POST['password']; // 

   // 
   $foto = $_FILES['fotouser'];
   $fotoRutaDB = null;
   $fotoNombre = null;

   if ($foto['error'] === UPLOAD_ERR_OK) {
       $tempFile = $foto['tmp_name'];
       $fotoNombre = basename($foto['name']);
       $uniqueFilename = uniqid() . '_' . $fotoNombre;
       $destination = $uploadDir . $uniqueFilename;

       if (move_uploaded_file($tempFile, $destination)) {
           $fotoRutaDB = $destination;
           error_log("Foto subida y guardada en: " . $fotoRutaDB); // 
       } else {
           error_log("Error al mover el archivo subido."); // 
           //
       }
   } elseif ($foto['error'] !== UPLOAD_ERR_NO_FILE) {
       error_log("Error al subir la foto: " . $foto['error']); // 
       // 
   }

   // 
   $sql = "UPDATE a SET nombre=?, apellidos=?, genero=?, cumpleanos=?";
   $params = [$name, $apellidos, $genero, $cumpleanos];
   $types = 'ssss';

   // 
   if (!empty($password)) {
       $sql .= ", password=?";
       $params[] = $password;
       $types .= 's';
   }

   // 
   if ($fotoRutaDB !== null) {
       $sql .= ", fotoRuta=?, fotoNombre=?";
       $params[] = $fotoRutaDB;
       $params[] = $fotoNombre;
       $types .= 'ss';
   }

   $sql .= " WHERE email=?";
   $params[] = $email;
   $types .= 's';

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // actualizar 
            $_SESSION['name'] = $name;
            $_SESSION['apellidos'] = $apellidos;
            $_SESSION['genero'] = $genero;
            $_SESSION['cumpleanos'] = $cumpleanos;

            // 
            header("Location: publicacion.php?mensaje=perfil_actualizado");
            exit();
        } else {
            echo "Error al actualizar el perfil: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error en la consulta SQL: " . $conn->error;
    }

} else {
    // 
    header("Location: editar_perfil.php");
    exit();
}

$conn->close();

?>