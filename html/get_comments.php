<?php

session_start(); // 

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['post_id']) && is_numeric($_GET['post_id']) && isset($_SESSION['user_id_mysql'])) {
    $postId = $_GET['post_id'];
    $userId = $_SESSION['user_id_mysql'];

    $sql = "SELECT
        c.id AS comment_id,
    c.comentario_text,
    c.tiempo_creacion,
    c.user_id AS comment_user_id, -- 
    c.user_id AS user_id,        -- 
    u.nombre AS user_name,
    u.fotoRuta AS user_photo,
    (SELECT COUNT(*) FROM likes_comentarios WHERE comentario_id = c.id) AS likes,
    (SELECT COUNT(*) FROM likes_comentarios WHERE comentario_id = c.id AND user_id = ?) AS has_liked
FROM comentarios c
JOIN a u ON c.user_id = u.id
WHERE c.post_id = ?
    ORDER BY c.tiempo_creacion ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'comments' => $comments]);

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de publicación inválido o usuario no logueado.']);
}

$db->closeConnection();
?>