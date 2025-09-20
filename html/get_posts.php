<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'php_errors.log');

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

$whereClause = "";
if (isset($_GET['categoria_id']) && $_GET['categoria_id'] !== '') {
    $categoryId = filter_var($_GET['categoria_id'], FILTER_SANITIZE_NUMBER_INT);
    $whereClause = "WHERE p.categoria_id = ?";
    $stmt = $conn->prepare("SELECT p.*, u.nombre AS nombre_usuario, u.fotoRuta, c.nombre AS categoria,
                                 (SELECT COUNT(1) FROM likes WHERE post_id = p.id) AS likes,
                                 (SELECT COUNT(1) FROM likes WHERE post_id = p.id AND user_id = ?) AS has_liked
                          FROM publicaciones p
                          JOIN a u ON p.user_id = u.id
                          JOIN categorias c ON p.categoria_id = c.id
                          $whereClause
                          ORDER BY p.tiempo_creacion DESC");
    $userId = $_SESSION['user_id_mysql'] ?? null;
    $stmt->bind_param("ii", $userId, $categoryId);
} else {
    $stmt = $conn->prepare("SELECT p.*, u.nombre AS nombre_usuario, u.fotoRuta, c.nombre AS categoria,
                                 (SELECT COUNT(1) FROM likes WHERE post_id = p.id) AS likes,
                                 (SELECT COUNT(1) FROM likes WHERE post_id = p.id AND user_id = ?) AS has_liked
                          FROM publicaciones p
                          JOIN a u ON p.user_id = u.id
                          JOIN categorias c ON p.categoria_id = c.id
                          ORDER BY p.tiempo_creacion DESC");
    $userId = $_SESSION['user_id_mysql'] ?? null;
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();
$db->closeConnection();

header('Content-Type: application/json');
echo json_encode($posts);
?>