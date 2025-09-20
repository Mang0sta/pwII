<?php

session_start();
// depurar
echo "SESSION ID en publicacion.php: " . session_id() . "<br>";
if (isset($_SESSION['user_id_mysql'])) {
    echo "USER ID en publicacion.php: " . $_SESSION['user_id_mysql'] . "<br>";
} else {
    echo "USER ID NO ESTÁ SETEADO en publicacion.php<br>";
}

if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    // 
    $nombreUsuario = "Invitado";
} else {
    // 
    $nombreUsuario = $_SESSION['name'];
}

require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

$stmt_categories = $conn->prepare("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();
$categories = [];
while ($row_cat = $result_categories->fetch_assoc()) {
    $categories[] = $row_cat;
}
$stmt_categories->close();
$db->closeConnection();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/publicacion.css">
    <link rel="stylesheet" type="text/css" href="../css/publicacion_dark.css" class="dark-mode-style" disabled>

    <style>
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 12px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none; 
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }
    </style>

    <script>
        function openModal() {
            document.getElementById("commentModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("commentModal").style.display = "none";
        }

        function addComment() {
            let commentText = document.getElementById("newComment").value;
            if (commentText) {
                let commentList = document.getElementById("commentList");
                let newComment = document.createElement("div");
                newComment.classList.add("comment-item");
                newComment.innerHTML = "<strong>Nuevo Usuario:</strong> " + commentText;
                commentList.appendChild(newComment);

                document.getElementById("newComment").value = "";

                closeModal();
            }
        }

        function publishPost() {
            const newPostTitulo = document.getElementById("newPostTitulo").value.trim();
            const newPostCategoria = document.getElementById("newPostCategoria").value;
            const newPostText = document.getElementById("newPostText").value.trim();
            const newPostImage = document.getElementById("newPostImage").files[0]; // obtener el archivo seleccionado

            if (newPostText) {
                const formData = new FormData();
                formData.append('newPostTitulo', newPostTitulo);
                formData.append('newPostCategoria', newPostCategoria);
                formData.append('newPostText', newPostText);
                formData.append('newPostImage', newPostImage); // agregar el archivo de imagen

                fetch('publish_post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("newPostTitulo").value = "";
                        document.getElementById("newPostCategoria").selectedIndex = 0;
                        document.getElementById("newPostText").value = "";
                        document.getElementById("newPostImage").value = ""; // limpiar el campo de archivo
                        loadPosts(); // recargar las publicaciones
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al publicar:', error);
                    alert('Ocurrió un error al publicar la entrada.');
                });
            } else {
                alert('Por favor, escribe algo para publicar.');
            }
        }

        function loadPosts() {
    fetch('get_posts.php')
        .then(response => response.json())
        .then(posts => {
            const postsContainer = document.querySelector('.container');
            postsContainer.innerHTML = '';
            posts.forEach(post => {
                const postDiv = document.createElement('div');
                postDiv.classList.add('post');
                const likeButtonClass = post.has_liked ? 'liked' : '';
                let imageHtml = '';
                if (post.ruta_imagen) {
                    imageHtml = `<img src="../${post.ruta_imagen}" alt="Imagen de la publicación" style="max-width:100%; border-radius:5px; margin-top: 10px;">`;
                }

                let userPhotoHtml = '';
                if (post.fotoRuta) {
                    const rutaFotoPerfil = post.fotoRuta.replace(/\\/g, '/').split('/').slice(-1).join('/');
                    userPhotoHtml = `<img src="uploads/profile_pics/${rutaFotoPerfil}" alt="Foto de perfil de ${post.nombre_usuario}" style="width: 100px; height: 100px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                } else {
                    userPhotoHtml = `<img src="user.jpg" alt="Foto de perfil por defecto" style="width: 100px; height: 100px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                }

                // formatear la fecha de creación 
                const fechaCreacion = new Date(post.tiempo_creacion);
                const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                const fechaFormateada = fechaCreacion.toLocaleDateString('es-MX', opcionesFecha);

                postDiv.innerHTML = `
                <div class="user-info">
                     <a href="perfil_usuario.php?user_id=${post.user_id}" style="text-decoration: none; color: inherit;">
                         ${userPhotoHtml}
                     </a>
                    <span>${post.nombre_usuario}</span>
                </div>
                    <h3>${post.titulo}</h3>
                    <div class="categories">
                        <a href="#">${post.categoria}</a>
                    </div>
                    <div class="post-date" style="font-size: 0.8em; color: #777; margin-bottom: 5px;">
                         ${fechaFormateada}
                    </div>
                    <div class="post-content">
                        <p>${post.contenido}</p>
                    </div>
                    ${imageHtml}
                    <div class="buttons">
                        <button class="like-button" data-post-id="${post.id}" onclick="toggleLike(${post.id}, this)">
                            👍 Like (${post.likes})
                        </button>
                        
                        
                        ${post.user_id === <?php echo isset($_SESSION['user_id_mysql']) ? $_SESSION['user_id_mysql'] : 'null'; ?> ?
                        `<button class="edit-button" data-post-id="${post.id}" onclick="openEditModal(${post.id})">Editar</button>
                         <button class="delete-button" data-post-id="${post.id}" onclick="deletePost(${post.id})">Eliminar</button>` : ''}
                    </div>
                    <div class="comment-section">
                        <div class="add-comment">
                            <textarea id="commentText_${post.id}" placeholder="Escribe un comentario..." style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; resize: none;"></textarea>
                            <button class="comment-button" onclick="addComment(${post.id})">Comentar</button>
                        </div>
                        <div class="comment-list-container" id="commentList_${post.id}">
                        </div>
                    </div>
                `;

                postsContainer.appendChild(postDiv);
                loadComments(post.id);
                const likeButton = postDiv.querySelector('.like-button');
                if (post.has_liked) {
                    likeButton.classList.add('liked');
                }
            });
        })
        .catch(error => {
            console.error('Error al cargar las publicaciones:', error);
        });
}
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

        document.addEventListener('DOMContentLoaded', loadPosts);

        function deletePost(postId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta publicación?')) {
        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadPosts(); // recarga publicaciones despues de eliminar 
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error al eliminar la publicación:', error);
            alert('Ocurrió un error al eliminar la publicación.');
        });
    }
}

    </script>
</head>
<body >
<header class="main-header">
  <a href="publicacion.php" class="logo">Social Link</a>
  <div class="nav-buttons">
  <select id="filterCategory" style="padding: 8px; border: 1px solid #ccc; border-radius: 5px; /* Ya no necesitas margin-right aquí si usas gap */" onchange="filterPostsByCategory(this.value)">
            <option value="">Todas las categorías</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
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
        <span><?= $nombreUsuario ?></span>
    </div>

    <div class="content">

        <div class="new-post-container" style="background-color: #fff; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
            <h3>Crear una publicación</h3>
            <input type="text" id="newPostTitulo" placeholder="Título de la publicación" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <label for="newPostCategoria" style="display: block; margin-bottom: 5px;">Categoría:</label>
            <select id="newPostCategoria" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea id="newPostText" placeholder="¿Qué estás pensando?" rows="3" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
            <label for="newPostImage" style="display: block; margin-bottom: 5px;">Subir imagen:</label>
            <input type="file" id="newPostImage" style="width: 100%; margin-bottom: 10px;">
            <button onclick="publishPost()" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Publicar</button>
        </div>

        <div class="container">
            </div>

        <div id="commentModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
            <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; border-radius: 5px; position: relative;">
                <span class="close" onclick="closeModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <h2>Escribe tu comentario</h2>
                <textarea id="newComment" placeholder="Escribe tu comentario..." rows="4" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                <button onclick="addComment()" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Enviar</button>
            </div>
        </div>
    </div>

    <div id="editPostModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; border-radius: 5px; position: relative;">
        <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2>Editar Publicación</h2>
        <form id="editPostForm">
            <input type="hidden" id="editPostId">
            <label for="editPostTitulo" style="display: block; margin-bottom: 5px;">Título:</label>
            <input type="text" id="editPostTitulo" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

            <label for="editPostCategoria" style="display: block; margin-bottom: 5px;">Categoría:</label>
            <select id="editPostCategoria" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="editPostText" style="display: block; margin-bottom: 5px;">Contenido:</label>
            <textarea id="editPostText" rows="4" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

            <label for="editPostImage" style="display: block; margin-bottom: 5px;">Cambiar imagen (opcional):</label>
            <input type="file" id="editPostImage" style="width: 100%; margin-bottom: 10px;">

            <button type="button" onclick="saveEditedPost()" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Guardar Cambios</button>
        </form>
    </div>
</div>


<div id="editCommentModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; border-radius: 5px; position: relative;">
        <span class="close" onclick="closeEditCommentModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2>Editar Comentario</h2>
        <textarea id="editCommentText" rows="4" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
        <input type="hidden" id="editingCommentId">
        <button onclick="saveEditedComment()" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Guardar Cambios</button>
    </div>
</div>

<script>
    

    function openEditModal(postId) {
        document.getElementById("editPostModal").style.display = "block";
        document.getElementById("editPostId").value = postId;
        // 
        loadPostDataForEdit(postId);
    }

    function closeEditModal() {
        document.getElementById("editPostModal").style.display = "none";
    }

    function loadPostDataForEdit(postId) {
        fetch(`get_post_details.php?id=${postId}`)
            .then(response => response.json())
            .then(post => {
                if (post) {
                    document.getElementById("editPostTitulo").value = post.titulo;
                    document.getElementById("editPostCategoria").value = post.categoria_id; // 
                    document.getElementById("editPostText").value = post.contenido;
                    // 
                } else {
                    alert('Error al cargar los detalles de la publicación.');
                    closeEditModal();
                }
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la publicación:', error);
                alert('Ocurrió un error al cargar los detalles de la publicación.');
                closeEditModal();
            });
    }

    function saveEditedPost() {
        const postId = document.getElementById("editPostId").value;
        const titulo = document.getElementById("editPostTitulo").value.trim();
        const categoriaId = document.getElementById("editPostCategoria").value;
        const contenido = document.getElementById("editPostText").value.trim();
        const nuevaImagen = document.getElementById("editPostImage").files[0];

        if (titulo && contenido) {
            const formData = new FormData();
            formData.append('postId', postId);
            formData.append('titulo', titulo);
            formData.append('categoriaId', categoriaId);
            formData.append('contenido', contenido);
            formData.append('nuevaImagen', nuevaImagen);

            fetch('edit_post.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    loadPosts(); // 
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al guardar la edición:', error);
                alert('Ocurrió un error al guardar la edición.');
            });
        } else {
            alert('Por favor, completa el título y el contenido de la publicación.');
        }
    }

    function closeEditModal() {
        document.getElementById("editPostModal").style.display = "none";
    }

    function addComment(postId) {
   // console.log("ID de la publicación al comentar:", postId); 
    const commentTextarea = document.getElementById(`commentText_${postId}`);
    const commentText = commentTextarea.value.trim();
   // console.log("Texto del comentario:", commentText);

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
                commentTextarea.value = ''; // 
                loadComments(postId); // 
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

                    let userPhotoLink = '';
                    if (comment.user_id == <?php echo isset($_SESSION['user_id_mysql']) ? $_SESSION['user_id_mysql'] : 'null'; ?>) {
                        userPhotoLink = `<a href="perfil_usuario.php?user_id=${comment.user_id}" style="text-decoration: none; color: inherit;">${userPhotoHtml}</a>`;
                    } else {
                        userPhotoLink = `<a href="perfil_usuario.php?user_id=${comment.user_id}" style="text-decoration: none; color: inherit;">${userPhotoHtml}</a>`;
                    }

                    // formatear la fecha de creación del comentario
                    const fechaCreacionComentario = new Date(comment.tiempo_creacion);
                    const opcionesFechaComentario = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                    const fechaFormateadaComentario = fechaCreacionComentario.toLocaleDateString('es-MX', opcionesFechaComentario);

                    const likeButtonClass = comment.has_liked ? 'liked' : '';
                    let editButtonHtml = '';
                    let deleteButtonHtml = '';
                    if (<?php echo isset($_SESSION['user_id_mysql']) ? $_SESSION['user_id_mysql'] : 'null'; ?> === comment.comment_user_id) {
                        editButtonHtml = `<button class="edit-comment-button" data-comment-id="${comment.comment_id}" onclick="openEditCommentModal(${comment.comment_id}, '${escapeHtml(comment.comentario_text)}')">Editar</button>`;
                        deleteButtonHtml = `<button class="delete-comment-button" data-comment-id="${comment.comment_id}" onclick="deleteComment(${comment.comment_id})">Borrar</button>`;
                    }
                    console.log('Enlace generado:', userPhotoLink);
                    commentDiv.innerHTML = `
                        ${userPhotoLink} <strong>${comment.user_name}:</strong> ${comment.comentario_text}
                        <div class="comment-date" style="font-size: 0.8em; color: #777; margin-top: 5px;">
                            ${fechaFormateadaComentario}
                        </div>
                        <button class="like-comment-button ${likeButtonClass}" data-comment-id="${comment.comment_id}" onclick="toggleLikeComment(${comment.comment_id}, this)">
                            👍 Like (${comment.likes})
                        </button>
                        ${editButtonHtml} ${deleteButtonHtml}
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
            const likeButton = button.querySelector('span') || button; // 
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
function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }

 function deleteComment(commentId) {
    if (confirm('¿Estás seguro de que deseas borrar este comentario?')) {
        fetch('delete_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `comment_id=${commentId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // recargar los comentarios de la publicación actual
                const postIdMatch = document.querySelector(`[data-comment-id="${commentId}"]`).closest('.comment-section').querySelector('.add-comment textarea').id.match(/commentText_(\d+)/);
                if (postIdMatch && postIdMatch[1]) {
                    loadComments(postIdMatch[1]);
                } else {
                    // si no se puede determinar el postId, recargar todas las publicaciones
                    loadPosts();
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error al borrar el comentario:', error);
            alert('Ocurrió un error al borrar el comentario.');
        });
    }
}

 function openEditCommentModal(commentId, commentText) {
    console.log("ID del comentario a editar:", commentId); // log de error
        document.getElementById("editCommentModal").style.display = "block";
        document.getElementById("editCommentText").value = commentText;
        document.getElementById("editingCommentId").value = commentId;
        console.log("Valor del ID del comentario guardado en el modal:", commentId); // log de error
    }

    function closeEditCommentModal() {
        document.getElementById("editCommentModal").style.display = "none";
    }

    function saveEditedComment() {
        const commentId = document.getElementById("editingCommentId").value;
        const editedText = document.getElementById("editCommentText").value.trim();

        console.log("ID del comentario a guardar:", commentId); // log

        if (editedText) {
            fetch('edit_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `comment_id=${commentId}&comentario_text=${encodeURIComponent(editedText)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditCommentModal();
                    // recargar los comentarios de la publicación actual
                    const postIdMatch = document.querySelector(`[data-comment-id="${commentId}"]`).closest('.comment-section').querySelector('.add-comment textarea').id.match(/commentText_(\d+)/);
                    if (postIdMatch && postIdMatch[1]) {
                        loadComments(postIdMatch[1]);
                    } else {
                        // 
                        loadPosts();
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al editar el comentario:', error);
                alert('Ocurrió un error al editar el comentario.');
            });
        } else {
            alert('El comentario no puede estar vacío.');
        }
    }

    function filterPostsByCategory(categoryId) {
    fetch(`get_posts.php?categoria_id=${categoryId}`)
        .then(response => response.json())
        .then(posts => {
            const postsContainer = document.querySelector('.container');
            postsContainer.innerHTML = ''; // limpiar el contenedor de publicaciones
            if (posts.length > 0) {
                posts.forEach(post => {
                    const postDiv = document.createElement('div');
                    postDiv.classList.add('post');
                    const likeButtonClass = post.has_liked ? 'liked' : '';
                    let imageHtml = '';
                    if (post.ruta_imagen) {
                        imageHtml = `<img src="../${post.ruta_imagen}" alt="Imagen de la publicación" style="max-width:100%; border-radius:5px; margin-top: 10px;">`;
                    }

                    let userPhotoHtml = '';
                    if (post.fotoRuta) {
                        const rutaFotoPerfil = post.fotoRuta.replace(/\\/g, '/').split('/').slice(-1).join('/');
                        userPhotoHtml = `<img src="uploads/profile_pics/${rutaFotoPerfil}" alt="Foto de perfil de ${post.nombre_usuario}" style="width: 100px; height: 100px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                    } else {
                        userPhotoHtml = `<img src="user.jpg" alt="Foto de perfil por defecto" style="width: 100px; height: 100px; border-radius: 50%; margin-right: 5px; vertical-align: middle;">`;
                    }

                    const fechaCreacion = new Date(post.tiempo_creacion);
                    const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                    const fechaFormateada = fechaCreacion.toLocaleDateString('es-MX', opcionesFecha);

                    postDiv.innerHTML = `
                    <div class="user-info">
                     <a href="perfil_usuario.php?user_id=${post.user_id}" style="text-decoration: none; color: inherit;">
                         ${userPhotoHtml}
                     </a>
                    <span>${post.nombre_usuario}</span>
                </div>
                        <h3>${post.titulo}</h3>
                        <div class="categories">
                            <a href="#">${post.categoria}</a>
                        </div>
                        <div class="post-date" style="font-size: 0.8em; color: #777; margin-bottom: 5px;">
                            ${fechaFormateada}
                        </div>
                        <div class="post-content">
                            <p>${post.contenido}</p>
                        </div>
                        ${imageHtml}
                        <div class="buttons">
                            <button class="like-button ${likeButtonClass}" data-post-id="${post.id}" onclick="toggleLike(${post.id}, this)">
                                👍 Like (${post.likes})
                            </button>
                            
                            ${post.user_id === <?php echo isset($_SESSION['user_id_mysql']) ? $_SESSION['user_id_mysql'] : 'null'; ?> ?
                            `<button class="edit-button" data-post-id="${post.id}" onclick="openEditModal(${post.id})">Editar</button>
                             <button class="delete-button" data-post-id="${post.id}" onclick="deletePost(${post.id})">Eliminar</button>` : ''}
                        </div>
                        <div class="comment-section">
                            <div class="add-comment">
                                <textarea id="commentText_${post.id}" placeholder="Escribe un comentario..." style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; resize: none;"></textarea>
                                <button class="comment-button" onclick="addComment(${post.id})">Comentar</button>
                            </div>
                            <div class="comment-list-container" id="commentList_${post.id}">
                            </div>
                        </div>
                    `;
                    postsContainer.appendChild(postDiv);
                    loadComments(post.id);
                    const likeButton = postDiv.querySelector('.like-button');
                    if (post.has_liked) {
                        likeButton.classList.add('liked');
                    }
                });
            } else {
                postsContainer.innerHTML = '<p>No hay publicaciones en esta categoría.</p>';
            }
        })
        .catch(error => {
            console.error('Error al cargar las publicaciones por categoría:', error);
            alert('Ocurrió un error al cargar las publicaciones.');
        });
}

/// modo oscuro
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeStyle = document.querySelector('.dark-mode-style');

    // comprobar si el modo oscuro estaba activado en la visita anterior
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