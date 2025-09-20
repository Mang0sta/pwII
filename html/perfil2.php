<?php
session_start();


if (!isset($_SESSION['user_id_mysql'])) {
    
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id_mysql'];
$nombreUsuario = $_SESSION['name'];

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();


$stmt_posts = $conn->prepare("SELECT
    p.id AS post_id, p.titulo, p.contenido, p.ruta_imagen, p.tiempo_creacion,
    c.nombre AS categoria, c.id AS categoria_id,
    u.nombre AS nombre_usuario, u.fotoRuta,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS has_liked
FROM publicaciones p
JOIN categorias c ON p.categoria_id = c.id
JOIN a u ON p.user_id = u.id
WHERE p.user_id = ?
ORDER BY p.tiempo_creacion DESC");
$stmt_posts->bind_param("ii", $userId, $userId);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();
$posts = [];
while ($row_post = $result_posts->fetch_assoc()) {
    $posts[] = $row_post;
}
$stmt_posts->close();


$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($nombreUsuario) ?></title>
    <link rel="stylesheet" href="../css/publicacion.css">
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
    </style>
</head>
<body style="position: relative;">
<header style="position: fixed; top: 0; left: 0; width: 100%; background-color: blue; padding: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); z-index: 1000; display: flex; justify-content: space-between; align-items: center;">
<a href="publicacion.php" style="font-size: 20px; font-weight: bold; margin-left: 10px; color: white; text-decoration: none;">Red Social</a>
    <div style="display: flex; align-items: center; gap: 15px;"> 
        <select id="filterCategory" style="padding: 8px; border: 1px solid #ccc; border-radius: 5px; /* Ya no necesitas margin-right aquí si usas gap */" onchange="filterPostsByCategory(this.value)">
            <option value="">Todas las categorías</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        
       
        
        <?php if (isset($_SESSION['email'])): ?>
            <a href="perfil2.php" class="logout-button" style="position: initial; padding: 8px 12px; background-color: #AECBFA; /* Un azul claro bonito */ color: #000080; /* Texto azul marino para contraste */ border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none;">Perfil</a>
            <a href="publicacion.php" class="logout-button" style="position: initial; padding: 8px 12px; background-color: #AECBFA; /* Un azul claro bonito */ color: #000080; /* Texto azul marino para contraste */ border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none;">Inicio</a>
            <a href="logout.php" class="logout-button" style="position: initial; padding: 8px 12px; background-color: #AECBFA; /* Un azul claro bonito */ color: #000080; /* Texto azul marino para contraste */ border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none;">Cerrar Sesión</a>
            <a href="chat.php" class="logout-button" style="position: initial; padding: 8px 12px; background-color: #AECBFA; /* Un azul claro bonito */ color: #000080; /* Texto azul marino para contraste */ border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none;">Chat</a>
            <a href="reportes.php" class="logout-button" style="position: initial; padding: 8px 12px; background-color: #AECBFA; /* Un azul claro bonito */ color: #000080; /* Texto azul marino para contraste */ border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none;">Reportes</a>
        <?php endif; ?>
    </div>
</header>
    <div class="main-container" style="margin-top: 70px;">
        <div class="sidebar">
            <span><?= htmlspecialchars($nombreUsuario) ?></span>
        </div>

        <div class="content">
            <div class="user-profile-info">
                <?php if (isset($_SESSION['fotoRuta'])): ?>
                    <?php $rutaFotoPerfil = str_replace('\\', '/', $_SESSION['fotoRuta']); ?>
                    <img src="uploads/profile_pics/<?= basename($rutaFotoPerfil) ?>" alt="Foto de perfil de <?= htmlspecialchars($nombreUsuario) ?>">
                <?php else: ?>
                    <img src="user.jpg" alt="Foto de perfil por defecto">
                <?php endif; ?>
                <h2>Perfil de <?= htmlspecialchars($nombreUsuario) ?></h2>
            </div>

            <h2>Mis Publicaciones</h2>
            <div class="container">
                <?php if (empty($posts)): ?>
                    <p>No has publicado nada aún.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="user-info">
                                <?php if ($post['fotoRuta']): ?>
                                    <?php $rutaFotoPerfil = str_replace('\\', '/', $post['fotoRuta']); ?>
                                    <img src="uploads/profile_pics/<?= basename($rutaFotoPerfil) ?>" alt="Foto de perfil de <?= htmlspecialchars($post['nombre_usuario']) ?>" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">
                                <?php else: ?>
                                    <img src="user.jpg" alt="Foto de perfil por defecto" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">
                                <?php endif; ?>
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
                                <button class="like-button <?= $post['has_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['post_id'] ?>" onclick="toggleLike(<?= $post['post_id'] ?>, this)">
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
                            const fechaCreacionComentario = new Date(comment.tiempo_creacion);
                            const opcionesFechaComentario = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                            const fechaFormateadaComentario = fechaCreacionComentario.toLocaleDateString('es-MX', opcionesFechaComentario);
                            const likeButtonClass = comment.has_liked ? 'liked' : '';
                            commentDiv.innerHTML = `
                                ${userPhotoHtml} <strong>${comment.user_name}:</strong> ${comment.comentario_text}
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

        // 
        document.addEventListener('DOMContentLoaded', function() {
            const postContainers = document.querySelectorAll('.post');
            postContainers.forEach(post => {
                const postId = post.querySelector('.like-button').getAttribute('data-post-id');
                loadComments(postId);
            });
        });
    </script>

    


</body>
</html>