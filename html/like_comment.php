<?php
session_start();
require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id_mysql']) && isset($_POST['comment_id']) && is_numeric($_POST['comment_id'])) {
    $commentId = intval($_POST['comment_id']);
    $userId = $_SESSION['user_id_mysql'];

    // verificar si ya existe el like
    $checkStmt = $conn->prepare("SELECT id FROM likes_comentarios WHERE user_id = ? AND comentario_id = ?");
    $checkStmt->bind_param("ii", $userId, $commentId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // ya le dio like entonces quitar el like
        $deleteStmt = $conn->prepare("DELETE FROM likes_comentarios WHERE user_id = ? AND comentario_id = ?");
        $deleteStmt->bind_param("ii", $userId, $commentId);
        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true, 'action' => 'unliked', 'comment_id' => $commentId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al quitar el like.']);
        }
        $deleteStmt->close();
    } else {
        // No le ha dado like agregar el like
        $insertStmt = $conn->prepare("INSERT INTO likes_comentarios (user_id, comentario_id) VALUES (?, ?)");
        $insertStmt->bind_param("ii", $userId, $commentId);
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true, 'action' => 'liked', 'comment_id' => $commentId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al dar like.']);
        }
        $insertStmt->close();
    }
    $checkStmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Petición inválida.']);
}

$db->closeConnection();
?>