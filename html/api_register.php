<?php
header('Content-Type: application/json');
session_start();
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    $error_message = "Error de conexión a la base de datos en api_register.php";
    error_log($error_message);
    http_response_code(500);
    echo json_encode(['error' => $error_message]);
    exit();
} else {
    error_log("Conexión a la base de datos exitosa en api_register.php");
}

$uploadDir = 'uploads/profile_pics/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    error_log("Directorio de subida creado: " . $uploadDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $cumpleanos = $_POST['cumpleanos'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $foto = $_FILES['foto'] ?? null;
    $fotoNombre = $_POST['fotoNombre'] ?? null;
    $fotoRutaDB = null;

    error_log("Datos recibidos en api_register.php:");
    error_log("Nombre: " . $name);
    error_log("Apellidos: " . $apellidos);
    error_log("Email: " . $email);
    error_log("Password (sin hash): " . $password);
    error_log("Cumpleaños: " . $cumpleanos);
    error_log("Género: " . $genero);
    error_log("Foto Nombre: " . $fotoNombre);

    if (!empty($name) && !empty($apellidos) && !empty($email) && !empty($password) && !empty($cumpleanos) && !empty($genero)) {
        // 
        if ($foto && $foto['error'] === 0) {
            $tempFile = $foto['tmp_name'];
            $fileExtension = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('profile_') . '.' . $fileExtension;
            $fotoRutaDB = $uploadDir . $newFileName;

            if (move_uploaded_file($tempFile, $fotoRutaDB)) {
                error_log("Foto subida exitosamente a: " . $fotoRutaDB);
            } else {
                error_log("Error al subir la foto.");
                $fotoRutaDB = null; // 
            }
        } else if ($foto && $foto['error'] !== 4) {
            error_log("Error al procesar la foto: " . $foto['error']);
            // 
        }

        $checkEmailStmt = $conn->prepare("SELECT email FROM a WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            http_response_code(409);
            $error_message = 'El email ya está registrado';
            echo json_encode(['error' => $error_message]);
            error_log($error_message);
        } else {
            
            $stmt = $conn->prepare("INSERT INTO a (nombre, apellidos, genero, email, password, cumpleanos, fotoRuta, fotoNombre) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $name, $apellidos, $genero, $email, $password, $cumpleanos, $fotoRutaDB, $fotoNombre);

            error_log("Consulta a ejecutar: INSERT INTO a (nombre, apellidos, genero, email, password, cumpleanos, fotoRuta, fotoNombre) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            error_log("Parámetros: Nombre: " . $name . ", Apellidos: " . $apellidos . ", Genero: " . $genero . ", Email: " . $email . ", Password (sin hash): " . $password . ", Cumpleanos: " . $cumpleanos . ", FotoRuta: " . $fotoRutaDB . ", FotoNombre: " . $fotoNombre);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
                error_log("Usuario registrado con éxito (sin hash de contraseña).");
            } else {
                http_response_code(500);
                $error_message = 'Error al registrar el usuario en la base de datos: ' . $stmt->error;
                echo json_encode(['error' => $error_message]);
                error_log($error_message);
            }
            $stmt->close();
        }
        $checkEmailStmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos obligatorios']);
        error_log("Error: Faltan campos obligatorios.");
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    error_log("Método no permitido en api_register.php (se esperaba POST)");
}

if ($conn) {
    $conn->close();
    error_log("Conexión a la base de datos cerrada en api_register.php");
}
?>