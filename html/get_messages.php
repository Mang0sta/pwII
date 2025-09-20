<?php
session_start();

// 
if (!isset($_SESSION['user_id_mysql']) || !isset($_GET['receiver_id'])) {
    echo "Error: Usuario no logueado o ID del receptor no proporcionado.";
    exit();
}

$senderId = $_SESSION['user_id_mysql'];
$receiverId = $_GET['receiver_id'];

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

// consulta para obtener los mensajes entre el emisor y el receptor
$sql_messages = "SELECT m.contenido, m.timestamp, u.id AS user_id, u.nombre AS nombre_usuario
                 FROM mensajes m
                 JOIN a u ON m.sender_id = u.id
                 WHERE (m.sender_id = ? AND m.receiver_id = ?)
                    OR (m.sender_id = ? AND m.receiver_id = ?)
                 ORDER BY m.timestamp ASC";

$stmt_messages = $conn->prepare($sql_messages);

if ($stmt_messages) {
    $stmt_messages->bind_param('iiii', $senderId, $receiverId, $receiverId, $senderId);
    $stmt_messages->execute();
    $result_messages = $stmt_messages->get_result();

    if ($result_messages) {
        $messages = $result_messages->fetch_all(MYSQLI_ASSOC);

        $output = '';
        foreach ($messages as $message) {
            $sent_received_class = ($message['user_id'] == $senderId) ? 'sent' : 'received';
            $output .= '<div class="message ' . $sent_received_class . '">';
            $output .= '<span class="message-content">' . htmlspecialchars($message['contenido']) . '</span>';
            $output .= '<span class="message-time">' . date('H:i', strtotime($message['timestamp'])) . '</span>';
            $output .= '</div>';
        }
        echo $output;

        $result_messages->free();
    } else {
        echo "Error al obtener los mensajes: " . $conn->error;
    }
    $stmt_messages->close();
} else {
    echo "Error al preparar la consulta de mensajes: " . $conn->error;
}

$db->closeConnection();
?>