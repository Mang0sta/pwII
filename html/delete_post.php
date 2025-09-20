<?php
session_start();

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verificar si el usuario está logueado
    if (!isset($_SESSION['user_id_mysql'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No estás autorizado para eliminar publicaciones.']);
        exit();
    }

    $postId = $_POST['post_id'] ?? null;
    $userId = $_SESSION['user_id_mysql'];

    if (!is_numeric($postId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de publicación inválido.']);
        exit();
    }

    // checar si la publicación pertenece al usuario actual
    $checkOwnershipSql = "SELECT id FROM publicaciones WHERE id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkOwnershipSql);
    $checkStmt->bind_param("ii", $postId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta publicación.']);
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();

    // eliminar la publicación
    $sql = "DELETE FROM publicaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Publicación eliminada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la publicación.']);
    }

    $stmt->close();
} else {
    http_response_code(405); // 
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

$db->closeConnection();
?>