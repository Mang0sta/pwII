<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id_mysql'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para comentar.']);
        exit();
    }

    $postId = $_POST['post_id'] ?? null;
    $comentario_text = $_POST['comentario_text'] ?? '';
    $userId = $_SESSION['user_id_mysql'];

    if (!is_numeric($postId) || empty(trim($comentario_text))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de publicación inválido o comentario vacío.']);
        exit();
    }

    $sql = "INSERT INTO comentarios (post_id, user_id, comentario_text, tiempo_creacion) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $postId, $userId, $comentario_text);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Comentario agregado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el comentario.']);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

$db->closeConnection();
?>