<?php
session_start();

// 
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    // 
    header("Location: publicacion.php");
    exit();
}

$userIdPerfil = $_GET['user_id'];

// 
if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    // 
    $nombreUsuario = "Invitado";
    $fotoRutaLogueado = null; // 
} else {
    // Si está logueado obtener el nombre 
    $nombreUsuario = $_SESSION['name'];
    require_once 'Database.php';
    $db_logueado = new Database();
    $conn_logueado = $db_logueado->getConnection();
    $emailLogueado = $_SESSION['email'];
    $stmt_logueado = $conn_logueado->prepare("SELECT fotoRuta FROM a WHERE email = ?");
    $stmt_logueado->bind_param("s", $emailLogueado);
    $stmt_logueado->execute();
    $result_logueado = $stmt_logueado->get_result();
    if ($row_logueado = $result_logueado->fetch_assoc()) {
        $fotoRutaLogueado = $row_logueado['fotoRuta'];
    } else {
        $fotoRutaLogueado = null; // 
    }
    $stmt_logueado->close();
    $db_logueado->closeConnection();
}

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

// obtener la información del usuario del perfil
$stmt_usuario = $conn->prepare("SELECT id, nombre, apellidos, genero, cumpleanos, fotoRuta FROM a WHERE id = ?");
$stmt_usuario->bind_param("i", $userIdPerfil);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
if ($row_usuario = $result_usuario->fetch_assoc()) {
    $nombreUsuarioPerfil = $row_usuario['nombre'];
    $apellidosUsuarioPerfil = $row_usuario['apellidos'];
    $generoUsuarioPerfil = $row_usuario['genero'];
    $cumpleanosUsuarioPerfil = $row_usuario['cumpleanos'];
    $fotoRutaPerfil = $row_usuario['fotoRuta'];
} else {
    // si el usuario no existe, redirigir a la página principal
    header("Location: publicacion.php");
    exit();
}
$stmt_usuario->close();

// obtener las publicaciones del usuario específico
$stmt_posts = $conn->prepare("SELECT
    p.id AS post_id, p.titulo, p.contenido, p.ruta_imagen, p.tiempo_creacion,
    c.nombre AS categoria, c.id AS categoria_id,
    u.id AS user_id, u.nombre AS nombre_usuario, u.fotoRuta,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS has_liked_by_me
FROM publicaciones p
JOIN categorias c ON p.categoria_id = c.id
JOIN a u ON p.user_id = u.id
WHERE p.user_id = ?
ORDER BY p.tiempo_creacion DESC");
$usuarioLogueadoId = isset($_SESSION['user_id_mysql']) ? $_SESSION['user_id_mysql'] : null;
$stmt_posts->bind_param("ii", $usuarioLogueadoId, $userIdPerfil);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();
$postsPerfil = [];
while ($row_post = $result_posts->fetch_assoc()) {
    $postsPerfil[] = $row_post;
}
$stmt_posts->close();

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($nombreUsuarioPerfil ?? 'Usuario') ?></title>
    <link rel="stylesheet" href="../css/publicacion.css">
    <link rel="stylesheet" type="text/css" href="../css/publicacion_dark.css" class="dark-mode-style" disabled>
    <style>
        .user-profile-info {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .user-profile-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .user-profile-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body style="position: relative;">
<header class="main-header">
  <a href="publicacion.php" class="logo">⚽ Stadium</a>
  <div class="nav-buttons">
  
    <?php if (isset($_SESSION['email'])): ?>
      <button id="darkModeToggle" class="dark-toggle">Modo Oscuro</button>
      <a href="publicacion.php" class="nav-btn">Inicio</a>
      <a href="perfil_usuario.php?user_id=<?= $_SESSION['user_id_mysql'] ?>" class="nav-btn">Perfil</a>
      <a href="perfil_editar.php" class="nav-btn">Perfil editar</a>
     
      
        <a href="reportes.php" class="nav-btn">Reportes</a>
      
      <a href="logout.php" class="nav-btn">Cerrar Sesión</a>
    <?php endif; ?>
  </div>
</header>
    <div class="main-container" style="margin-top: 70px;">
        <div class="sidebar">
            <span><?= htmlspecialchars($nombreUsuario) ?></span>
        </div>

        <div class="content">
        <div class="user-profile-info">
    <?php if ($fotoRutaPerfil): ?>
        <img src="<?= htmlspecialchars(str_replace('\\', '/', $fotoRutaPerfil)) ?>" alt="Foto de perfil de <?= htmlspecialchars($nombreUsuarioPerfil ?? 'Usuario') ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
    <?php else: ?>
        <img src="user.jpg" alt="Foto de perfil por defecto" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
    <?php endif; ?>
    <h2>Perfil de <?= htmlspecialchars($nombreUsuarioPerfil ?? 'Usuario') ?></h2>
    <?php if (!empty($apellidosUsuarioPerfil)): ?>
        <p>Apellidos: <?= htmlspecialchars($apellidosUsuarioPerfil) ?></p>
    <?php endif; ?>
    <?php if (!empty($generoUsuarioPerfil)): ?>
        <p>Género: <?= htmlspecialchars($generoUsuarioPerfil) ?></p>
    <?php endif; ?>
    <?php if (!empty($cumpleanosUsuarioPerfil)): ?>
        <?php
        $fechaCumpleanos = new DateTime($cumpleanosUsuarioPerfil);
        $fechaFormateada = $fechaCumpleanos->format('d M Y');
        ?>
        <p>Cumpleaños: <?= $fechaFormateada ?></p>
    <?php endif; ?>
</div>

            <h2>Publicaciones de <?= htmlspecialchars($nombreUsuarioPerfil ?? 'Usuario') ?></h2>
            <div class="container">
                <?php if (empty($postsPerfil)): ?>
                    <p>Este usuario no ha publicado nada aún.</p>
                <?php else: ?>
                    <?php foreach ($postsPerfil as $post): ?>
                        <div class="post">
                            <div class="user-info">
                                <a href="perfil_usuario.php?user_id=<?= htmlspecialchars($post['user_id']) ?>" class="user-profile-link">
                                    <?php if ($post['fotoRuta']): ?>
                                        <?php $rutaFotoPerfil = str_replace('\\', '/', $post['fotoRuta']); ?>
                                        <img src="uploads/profile_pics/<?= basename($rutaFotoPerfil) ?>" alt="Foto de perfil de <?= htmlspecialchars($post['nombre_usuario']) ?>" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">
                                    <?php else: ?>
                                        <img src="user.jpg" alt="Foto de perfil por defecto" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">
                                    <?php endif; ?>
                                </a>
                                <span><?= htmlspecialchars($post['nombre_usuario']) ?></span>
                            </div>
                            <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                            <div class="categories">
                                <a href="#"><?= htmlspecialchars($post['categoria']) ?></a>
                                
                            </div>
                            <div class="post-date" style="font-size: 0.8em; color: #777; margin-bottom: 5px;">
                                <?php
                                $fechaCreacion = new DateTime($post['tiempo_creacion']);
                                echo $fechaCreacion->format('d M Y H:i');
                                ?>
                            </div>
                            <div class="post-content">
                                <p><?= htmlspecialchars($post['contenido']) ?></p>
                            </div>
                            <?php if ($post['ruta_imagen']): ?>
                                <img src="../<?= htmlspecialchars($post['ruta_imagen']) ?>" alt="Imagen de la publicación" style="max-width:100%; border-radius:5px; margin-top: 10px;">
                            <?php endif; ?>
                            <div class="buttons">
                                <button class="like-button <?= $post['has_liked_by_me'] ? 'liked' : '' ?>" data-post-id="<?= $post['post_id'] ?>" onclick="toggleLike(<?= $post['post_id'] ?>, this)">
                                    👍 Like (<?= $post['likes'] ?>)
                                </button>
                                </div>
                            <div class="comment-section">
                                <div class="comment-list-container" id="commentList_<?= $post['post_id'] ?>">
                                    </div>
                                <div class="add-comment">
                                    <textarea id="commentText_<?= $post['post_id'] ?>" placeholder="Escribe un comentario..." style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; resize: none;"></textarea>
                                    <button class="comment-button" onclick="addComment(<?= $post['post_id'] ?>)">Comentar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-sidebar">
        </div>
    </div>

    <script>
        // 
        function toggleLike(postId, button) {
            const action = button.classList.contains('liked') ? 'unlike' : 'like';
            fetch('like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likesMatch = button.textContent.match(/\((\d+)\)/);
                    let currentLikes = likesMatch ? parseInt(likesMatch[1]) : 0;
                    if (data.action === 'like') {
                        button.classList.add('liked');
                        button.textContent = `👍 Like (${currentLikes + 1})`;
                    } else {
                        button.classList.remove('liked');
                        button.textContent = `👍 Like (${currentLikes > 0 ? currentLikes - 1 : 0})`;
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al dar/quitar like:', error);
                alert('Ocurrió un error al procesar el like.');
            });
        }

        function addComment(postId) {
            const commentTextarea = document.getElementById(`commentText_${postId}`);
            const commentText = commentTextarea.value.trim();
            if (commentText !== '') {
                fetch('add_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `post_id=${postId}&comentario_text=${encodeURIComponent(commentText)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        commentTextarea.value = '';
                        loadComments(postId);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al agregar el comentario:', error);
                    alert('Ocurrió un error al agregar el comentario.');
                });
            } else {
                alert('Por favor, escribe un comentario.');
            }
        }

        function loadComments(postId) {
            const commentListContainer = document.getElementById(`commentList_${postId}`);
            commentListContainer.innerHTML = '<p>Cargando comentarios...</p>';
            fetch(`get_comments.php?post_id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    commentListContainer.innerHTML = '';
                    if (data.success && data.comments.length > 0) {
                        data.comments.forEach(comment => {
                            const commentDiv = document.createElement('div');
                            commentDiv.classList.add('comment-item');
                            let userPhotoHtml = '';
                            if (comment.user_photo) {
                                const rutaFotoPerfil = comment.user_photo.replace(/\\/g, '/').split('/').slice(-1).join('/');
                                userPhotoHtml = `<img src="uploads/profile_pics/${rutaFotoPerfil}" alt="Foto de perfil de ${comment.user_name}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                            } else {
                                userPhotoHtml = `<img src="user.jpg" alt="Foto de perfil por defecto" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                            }
                            const userPhotoLink = `<a href="perfil_usuario.php?user_id=${comment.user_id}" class="user-profile-link">${userPhotoHtml}</a>`;
                            const fechaCreacionComentario = new Date(comment.tiempo_creacion);
                            const opcionesFechaComentario = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                            const fechaFormateadaComentario = fechaCreacionComentario.toLocaleDateString('es-MX', opcionesFechaComentario);
                            const likeButtonClass = comment.has_liked ? 'liked' : '';
                            commentDiv.innerHTML = `
                                ${userPhotoLink} <strong>${comment.user_name}:</strong> ${comment.comentario_text}
                                <div class="comment-date" style="font-size: 0.8em; color: #777; margin-top: 5px;">
                                    ${fechaFormateadaComentario}
                                </div>
                                <button class="like-comment-button ${likeButtonClass}" data-comment-id="${comment.comment_id}" onclick="toggleLikeComment(${comment.comment_id}, this)">
                                    👍 Like (${comment.likes})
                                </button>
                            `;
                            commentListContainer.appendChild(commentDiv);
                        });
                    } else if (data.success && data.comments.length === 0) {
                        commentListContainer.innerHTML = '<p>No hay comentarios aún.</p>';
                    } else {
                        commentListContainer.innerHTML = '<p>Error al cargar los comentarios.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los comentarios:', error);
                    commentListContainer.innerHTML = '<p>Error al cargar los comentarios.</p>';
                });
        }

        function toggleLikeComment(commentId, button) {
            fetch('like_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `comment_id=${commentId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeButton = button.querySelector('span') || button;
                    const likeCountMatch = likeButton.textContent.match(/\((\d+)\)/);
                    let currentLikes = likeCountMatch ? parseInt(likeCountMatch[1]) : 0;
                    if (data.action === 'liked') {
                        button.classList.add('liked');
                        likeButton.textContent = `👍 Like (${currentLikes + 1})`;
                    } else if (data.action === 'unliked') {
                        button.classList.remove('liked');
                        likeButton.textContent = `👍 Like (${currentLikes - 1})`;
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al dar/quitar like al comentario:', error);
                alert('Ocurrió un error al dar/quitar like al comentario.');
            });
        }

        // carga los comentarios iniciales para cada publicación
        document.addEventListener('DOMContentLoaded', function() {
            const postContainers = document.querySelectorAll('.post');
            postContainers.forEach(post => {
                const postId = post.querySelector('.like-button').getAttribute('data-post-id');
                loadComments(postId);
            });
        });

// modo oscuro
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeStyle = document.querySelector('.dark-mode-style');

    // 
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

</body>
</html>