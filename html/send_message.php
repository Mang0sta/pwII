<?php
session_start();

// 
if (!isset($_SESSION['user_id_mysql']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo "Error: Usuario no logueado, ID del receptor o mensaje no proporcionado.";
    exit();
}

$senderId = $_SESSION['user_id_mysql'];
$receiverId = $_POST['receiver_id'];
$message = $_POST['message'];

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

// 
$sql_insert = "INSERT INTO mensajes (sender_id, receiver_id, contenido, timestamp)
               VALUES (?, ?, ?, NOW())";

$stmt_insert = $conn->prepare($sql_insert);

if ($stmt_insert) {
    $stmt_insert->bind_param('iis', $senderId, $receiverId, $message);
    $stmt_insert->execute();

    if ($stmt_insert->affected_rows > 0) {
        echo "success"; // 
    } else {
        echo "Error al guardar el mensaje: " . $conn->error;
    }
    $stmt_insert->close();
} else {
    echo "Error al preparar la consulta para enviar el mensaje: " . $conn->error;
}

$db->closeConnection();
?>