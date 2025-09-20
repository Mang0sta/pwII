<?php
session_start();

// 
if (!isset($_SESSION['user_id_mysql'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit();
}

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

$userId = $_SESSION['user_id_mysql'];
$postId = $_POST['post_id'] ?? null;
$action = $_POST['action'] ?? null; // 'like' o 'unlike'

if ($postId && $action) {
    if ($action === 'like') {
        $stmt_check = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt_check->bind_param("ii", $userId, $postId);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows === 0) {
            $stmt_like = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
            $stmt_like->bind_param("ii", $userId, $postId);
            if ($stmt_like->execute()) {
                $stmt_update_likes = $conn->prepare("UPDATE publicaciones SET likes = likes + 1 WHERE id = ?");
                $stmt_update_likes->bind_param("i", $postId);
                $stmt_update_likes->execute();
                echo json_encode(['success' => true, 'action' => 'like']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al dar like: ' . $stmt_like->error]);
            }
            $stmt_like->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Ya le has dado like a esta publicación.']);
        }
        $stmt_check->close();
    } elseif ($action === 'unlike') {
        $stmt_unlike = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt_unlike->bind_param("ii", $userId, $postId);
        if ($stmt_unlike->execute()) {
            $stmt_update_likes = $conn->prepare("UPDATE publicaciones SET likes = likes - 1 WHERE id = ? AND likes > 0");
            $stmt_update_likes->bind_param("i", $postId);
            $stmt_update_likes->execute();
            echo json_encode(['success' => true, 'action' => 'unlike']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al quitar el like: ' . $stmt_unlike->error]);
        }
        $stmt_unlike->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de publicación o acción faltante.']);
}

$db->closeConnection();
?>