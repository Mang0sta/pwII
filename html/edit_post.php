<?php
session_start();

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // checar si el usuario está logueado
    if (!isset($_SESSION['user_id_mysql'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No estás autorizado para editar publicaciones.']);
        exit();
    }

    $postId = $_POST['postId'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $categoriaId = $_POST['categoriaId'] ?? null;
    $contenido = $_POST['contenido'] ?? '';
    $userId = $_SESSION['user_id_mysql'];

    // 
    if (!is_numeric($postId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de publicación inválido.']);
        exit();
    }

    // campos no deben estar vacios
    if (empty($titulo) || empty($contenido) || !is_numeric($categoriaId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos.']);
        exit();
    }

    error_log("Contenido recibido en edit_post.php: " . $_POST['contenido']); // log para probar
    $sql = "UPDATE publicaciones SET titulo = ?, categoria_id = ?, contenido = ?, fecha_actualizacion = NOW() WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $titulo, $categoriaId, $contenido, $postId, $userId);

    if ($stmt->execute()) {
        // checar la nueva imagen si se subio una
        if (isset($_FILES['nuevaImagen']) && $_FILES['nuevaImagen']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['nuevaImagen']['name'];
            $tipoArchivo = $_FILES['nuevaImagen']['type'];
            $tamanoArchivo = $_FILES['nuevaImagen']['size'];
            $rutaTemporal = $_FILES['nuevaImagen']['tmp_name'];

            // validar tipo de archivo 
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($tipoArchivo, $tiposPermitidos)) {
                // validar tamaño del archivo 
                if ($tamanoArchivo <= 2 * 1024 * 1024) { // 2MB
                    $nombreUnico = uniqid('post_') . '_' . $nombreArchivo;
                    $rutaDestino = '../uploads/' . $nombreUnico;

                    // mover el archivo subido a la ubicación deseada
                    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                        // actualizar la ruta de la imagen en la base de datos
                        $rutaImagenParaBD = 'uploads/' . $nombreUnico; // 
                        $sql_imagen = "UPDATE publicaciones SET ruta_imagen = ? WHERE id = ?";
                        $stmt_imagen = $conn->prepare($sql_imagen);
                        $stmt_imagen->bind_param("si", $rutaImagenParaBD, $postId); // 
                        $stmt_imagen->execute();
                        $stmt_imagen->close();
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al guardar la nueva imagen.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'La nueva imagen es demasiado grande.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Formato de la nueva imagen no permitido.']);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Publicación actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la publicación.']);
    }

    $stmt->close();
} else {
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

$db->closeConnection();
?>