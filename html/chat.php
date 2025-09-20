<?php
session_start();

// 
if (!isset($_SESSION['user_id_mysql'])) {
    header("Location: login.php"); // 
    exit();
}

$userId = $_SESSION['user_id_mysql'];

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

// 
$receiverName = "Selecciona un usuario para chatear";
$messages = [];

// 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="../css/chat.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="../css/chat_dark.css" class="dark-mode-style" disabled>
   
</head>
<body>
<header class="main-header">
  <a href="publicacion.php" class="logo">Social Link</a>
  <div class="nav-buttons">
    <?php if (isset($_SESSION['email'])): ?>
      <button id="darkModeToggle" class="dark-toggle">Modo Oscuro</button>
      <a href="publicacion.php" class="nav-btn">Inicio</a>
      <a href="perfil_usuario.php?user_id=<?= $_SESSION['user_id_mysql'] ?>" class="nav-btn">Perfil</a>
      <a href="perfil_editar.php" class="nav-btn">Perfil editar</a>
      <a href="chat.php" class="nav-btn">Chat</a>
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <a href="reportes.php" class="nav-btn">Reportes</a>
      <?php endif; ?>
      <a href="logout.php" class="nav-btn">Cerrar Sesión</a>
    <?php endif; ?>
  </div>
</header>

    

    <div class="chat-container">
        <div class="users-list">
            <h2>Usuarios</h2>
            <?php
            // Obtener la lista de usuarios de la base de datos usando MySQLi
            $sql_users = "SELECT id, nombre FROM a WHERE id != ?"; // 
            $stmt_users = $conn->prepare($sql_users);

            if ($stmt_users) {
                $stmt_users->bind_param('i', $userId); // 
                $stmt_users->execute();
                $result_users = $stmt_users->get_result(); // obtener el resultado

                if ($result_users) {
                    $users = $result_users->fetch_all(MYSQLI_ASSOC); // obtener todos los usuarios como un array asociativo

                    if ($users) {
                        foreach ($users as $user) {
                            echo '<div class="user-item" data-user-id="' . $user['id'] . '">' . htmlspecialchars($user['nombre']) . '</div>';
                        }
                    } else {
                        echo '<p>No hay otros usuarios registrados.</p>';
                    }
                    $result_users->free(); // 
                } else {
                    echo "Error al obtener la lista de usuarios: " . $conn->error;
                }
                $stmt_users->close(); // 
            } else {
                echo "Error al preparar la consulta de usuarios: " . $conn->error;
            }
            ?>
        </div>
        <div class="chat-area">
            <div class="chat-header">
                <h3><?= htmlspecialchars($receiverName) ?></h3>
            </div>
            <div class="message-area">
                <p>No hay mensajes con este usuario.</p>
            </div>
            <div class="input-area">
                <form id="send-message-form">
                <input type="hidden" id="receiver-id" name="receiver_id">
                <input type="text" id="message-input" class="message-input" placeholder="Escribe tu mensaje...">
                <input type="file" id="image-input" name="image" accept="image/*">
                 <button type="submit" class="send-button">Enviar</button>
    </form>
</div>
        </div>
    </div>

    <script>
        const userItems = document.querySelectorAll('.user-item');
        const chatHeader = document.querySelector('.chat-header h3');
        const messageArea = document.querySelector('.message-area');
        const receiverIdInput = document.getElementById('receiver-id');
        const sendMessageForm = document.getElementById('send-message-form');
        const messageInput = document.getElementById('message-input');

        let currentReceiverId = null;

        userItems.forEach(userItem => {
            userItem.addEventListener('click', function() {
                currentReceiverId = this.dataset.userId;
                chatHeader.textContent = 'Chat con ' + this.textContent;
                receiverIdInput.value = currentReceiverId;
                // carga mensajes
                loadMessages(currentReceiverId);
            });
        });

        sendMessageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (currentReceiverId && messageInput.value.trim() !== '') {
                const message = messageInput.value.trim();
                sendMessage(currentReceiverId, message);
                messageInput.value = ''; // limpiar input
            } else if (!currentReceiverId) {
                alert('Por favor, selecciona un usuario para enviar el mensaje.');
            } else {
                alert('Por favor, escribe un mensaje.');
            }
        });

        function loadMessages(receiverId) {
            fetch('get_messages.php?receiver_id=' + receiverId)
                .then(response => response.text())
                .then(data => {
                    messageArea.innerHTML = data;
                    messageArea.scrollTop = messageArea.scrollHeight; // scroll
                })
                .catch(error => console.error('Error al cargar los mensajes:', error));
        }

        function sendMessage(receiverId, message) {
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'receiver_id=' + receiverId + '&message=' + encodeURIComponent(message)
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    loadMessages(receiverId); // recarga los mensajes
                } else {
                    alert('Error al enviar el mensaje.');
                }
            })
            .catch(error => console.error('Error al enviar el mensaje:', error));
        }

       

            //modo oscuro funcion
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeStyle = document.querySelector('.dark-mode-style');

    
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
    if (isDarkMode) {
        darkModeStyle.disabled = false;
        darkModeToggle.textContent = 'Desactivar Modo Oscuro';
    }

    darkModeToggle.addEventListener('click', function() {
        darkModeStyle.disabled = !darkModeStyle.disabled;
        const isCurrentlyDark = !darkModeStyle.disabled;
        localStorage.setItem('darkMode', isCurrentlyDark ? 'enabled' : 'disabled');
        this.textContent = isCurrentlyDark ? 'Desactivar Modo Oscuro' : 'Activar Modo Oscuro';
    });
});



    </script>

<script src="../js/chat.js"></script>
</body>
</html>