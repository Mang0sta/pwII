<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id_mysql'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit();
}

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

$userId = $_SESSION['user_id_mysql'];
$titulo_raw = $_POST['newPostTitulo'] ?? '';
$categoria_raw = $_POST['newPostCategoria'] ?? '';
$contenido_raw = $_POST['newPostText'] ?? '';

if (empty(trim($titulo_raw))) {
    echo json_encode(['success' => false, 'message' => 'El título de la publicación es obligatorio.']);
    exit();
}

if (empty(trim($contenido_raw))) {
    echo json_encode(['success' => false, 'message' => 'El contenido de la publicación es obligatorio.']);
    exit();
}

$ruta_imagen = null; // 

// 
if (isset($_FILES['newPostImage']) && $_FILES['newPostImage']['error'] === UPLOAD_ERR_OK) {
    $imagen = $_FILES['newPostImage'];
    $nombre_temporal = $imagen['tmp_name'];
    $nombre_archivo = basename($imagen['name']);
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

    // 
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($extension, $extensiones_permitidas)) {
        // 
        $nombre_unico = uniqid('post_', true) . '.' . $extension;
        $ruta_destino = '../uploads/' . $nombre_unico; // 

        // 
        if (move_uploaded_file($nombre_temporal, $ruta_destino)) {
            $ruta_imagen = 'uploads/' . $nombre_unico; // guardar la ruta relativa en la base de datos
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido.']);
        exit();
    }
}

// 
$titulo = $conn->real_escape_string($titulo_raw);
$categoria = $conn->real_escape_string($categoria_raw);
$contenido = $conn->real_escape_string($contenido_raw);

// 
$query = "INSERT INTO publicaciones (user_id, titulo, categoria_id, contenido, ruta_imagen) VALUES ($userId, '$titulo', $categoria, '$contenido', '$ruta_imagen')";

if ($conn->query($query) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Publicación creada exitosamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear la publicación: ' . $conn->error]);
}

$db->closeConnection();
?>