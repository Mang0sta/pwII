<?php
session_start();

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id_mysql'] ?? null;

    $sql = "SELECT p.id, p.titulo, p.categoria_id, c.nombre AS categoria, p.contenido, p.ruta_imagen, p.user_id
            FROM publicaciones p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $postDetails = $result->fetch_assoc();

    error_log("--- Depuración get_post_details.php ---");
    error_log("Post ID recibido: " . $postId);
    error_log("User ID de la sesión (\$_SESSION['user_id_mysql']): " . $userId);

    if ($postDetails) {
        error_log("User ID de la publicación (\$postDetails['user_id']): " . $postDetails['user_id']);
        error_log("Tipo de \$postDetails['user_id']: " . gettype($postDetails['user_id']));
        error_log("Tipo de \$userId: " . gettype($userId));

        if ($postDetails['user_id'] === $userId) {
            error_log("Coincidencia de User IDs: TRUE");
            header('Content-Type: application/json');
            echo json_encode($postDetails);
        } else {
            error_log("Coincidencia de User IDs: FALSE");
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['message' => 'No tienes permiso para editar esta publicación.']);
        }
    } else {
        error_log("Publicación con ID " . $postId + " no encontrada.");
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['message' => 'Publicación no encontrada.']);
    }

    $stmt->close();
} else {
    error_log("ID de publicación inválido en la petición GET.");
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['message' => 'ID de publicación inválido.']);
}

$db->closeConnection();
?>