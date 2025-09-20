<?php
session_start();
require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id_mysql'])) {
    if (!isset($_POST['comment_id']) || !is_numeric($_POST['comment_id']) || $_POST['comment_id'] <= 0) {
        $response = array('success' => false, 'message' => 'ID de comentario inválido.');
        echo json_encode($response);
        exit();
    }
    $commentId = intval($_POST['comment_id']);
    $userId = $_SESSION['user_id_mysql'];

    // checar si el comentario pertenece al usuario logeado
    $checkStmt = $conn->prepare("SELECT user_id FROM comentarios WHERE id = ?");
    $checkStmt->bind_param("i", $commentId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 1) {
        $row = $checkResult->fetch_assoc();
        if ($row['user_id'] === $userId) {
            $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ?");
            $stmt->bind_param("i", $commentId);

            if ($stmt->execute()) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => 'Error al borrar el comentario.');
            }
            $stmt->close();
        } else {
            $response = array('success' => false, 'message' => 'No tienes permiso para borrar este comentario.');
        }
    } else {
        $response = array('success' => false, 'message' => 'Comentario no encontrado.');
    }
    $checkStmt->close();

} else {
    $response = array('success' => false, 'message' => 'Petición inválida o usuario no logueado.');
}

$db->closeConnection();
echo json_encode($response);
?>