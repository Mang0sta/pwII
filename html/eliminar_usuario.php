<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'Database.php';
$database = new Database();
$conn = $database->getConnection();

$email = $_SESSION['email'];

// obtener el ID del usuario 
$stmt_user_id = $conn->prepare("SELECT id, fotoRuta FROM a WHERE email = ?");
$stmt_user_id->bind_param("s", $email);
$stmt_user_id->execute();
$result_user_id = $stmt_user_id->get_result();

if ($result_user_id->num_rows === 1) {
    $row_user_id = $result_user_id->fetch_assoc();
    $user_id = $row_user_id['id'];
    $fotoRuta = $row_user_id['fotoRuta'];

    //  elimina los likes del usuario
    $stmt_delete_user_likes = $conn->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt_delete_user_likes->bind_param("i", $user_id);
    $stmt_delete_user_likes->execute();
    $stmt_delete_user_likes->close();

    //  Eliminar mensajes enviados
    $stmt_delete_user_messages_sent = $conn->prepare("DELETE FROM mensajes WHERE sender_id = ?");
    $stmt_delete_user_messages_sent->bind_param("i", $user_id);
    $stmt_delete_user_messages_sent->execute();
    $stmt_delete_user_messages_sent->close();

    // Eliminar mensajes recibidos
    $stmt_delete_user_messages_received = $conn->prepare("DELETE FROM mensajes WHERE receiver_id = ?");
    $stmt_delete_user_messages_received->bind_param("i", $user_id);
    $stmt_delete_user_messages_received->execute();
    $stmt_delete_user_messages_received->close();

    //  obtiene todas las IDs de las publicaciones del usuario
    $stmt_get_posts = $conn->prepare("SELECT id, ruta_imagen FROM publicaciones WHERE user_id = ?");
    $stmt_get_posts->bind_param("i", $user_id);
    $stmt_get_posts->execute();
    $result_get_posts = $stmt_get_posts->get_result();
    $post_ids_to_delete = [];
    $image_paths_to_delete = [];
    while ($row_post = $result_get_posts->fetch_assoc()) {
        $post_ids_to_delete[] = $row_post['id'];
        if (!empty($row_post['ruta_imagen'])) {
            $image_paths_to_delete[] = '../' . $row_post['ruta_imagen'];
        }
    }
    $stmt_get_posts->close();

    //  eliminar likes de las publicaciones del usuario
    if (!empty($post_ids_to_delete)) {
        $stmt_delete_post_likes = $conn->prepare("DELETE FROM likes WHERE post_id IN (" . implode(',', array_fill(0, count($post_ids_to_delete), '?')) . ")");
        $stmt_delete_post_likes->bind_param(str_repeat('i', count($post_ids_to_delete)), ...$post_ids_to_delete);
        $stmt_delete_post_likes->execute();
        $stmt_delete_post_likes->close();
    }

    // eliminar comentarios del usuario
    $stmt_delete_comments = $conn->prepare("DELETE FROM comentarios WHERE user_id = ?");
    $stmt_delete_comments->bind_param("i", $user_id);
    $stmt_delete_comments->execute();
    $stmt_delete_comments->close();

    // eliminar likes del usuario en comentarios
    $stmt_delete_comment_likes = $conn->prepare("DELETE FROM likes_comentarios WHERE user_id = ?");
    $stmt_delete_comment_likes->bind_param("i", $user_id);
    $stmt_delete_comment_likes->execute();
    $stmt_delete_comment_likes->close();

    // eliminar las publicaciones del usuario
    $stmt_delete_posts = $conn->prepare("DELETE FROM publicaciones WHERE user_id = ?");
    $stmt_delete_posts->bind_param("i", $user_id);
    $stmt_delete_posts->execute();
    $stmt_delete_posts->close();

    // eliminar al usuario
    $stmt_delete_user = $conn->prepare("DELETE FROM a WHERE email = ?");
    $stmt_delete_user->bind_param("s", $email);
    if ($stmt_delete_user->execute()) {
        // eliminar la foto de perfil si existe
        if (!empty($fotoRuta) && file_exists('../' . $fotoRuta)) {
            unlink('../' . $fotoRuta);
        }
        // eliminar las imágenes de las publicaciones del usuario
        foreach ($image_paths_to_delete as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Destruye la sesion
        session_destroy();
        header("Location: index.php?cuenta_eliminada=1");
        exit();
    } else {
        // 
        $_SESSION['delete_error'] = 'Error al eliminar la cuenta.';
        header("Location: perfil_editar.php");
        exit();
    }
    //$stmt_delete_user->close();
} else {
    // error
    $_SESSION['delete_error'] = 'No se encontró la cuenta para eliminar.';
    header("Location: perfil_editar.php");
    exit();
}

//$database->closeConnection();
?>