<?php

session_start();

//require 'rol_middleware.php';
//requireRole('admin');

















if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    // 
    $nombreUsuario = "Invitado";
} else {
    // 
    $nombreUsuario = $_SESSION['name'];
}

require_once 'Database.php';
$db_report = new Database();
$conn_report = $db_report->getConnection();

// Consulta para usuarios
$sql_usuarios = "SELECT id, nombre, apellidos, genero, email, password, cumpleanos, rol FROM a";
$result_usuarios = $conn_report->query($sql_usuarios);
$num_usuarios = $result_usuarios ? $result_usuarios->num_rows : 0;

// 
$sql_publicaciones = "SELECT p.id, u.fotoRuta AS ruta_foto_usuario, u.nombre AS nombre_usuario, c.nombre AS nombre_categoria, p.titulo, p.contenido, p.likes, p.tiempo_creacion, p.fecha_actualizacion
                       FROM publicaciones p
                       JOIN a u ON p.user_id = u.id
                       JOIN categorias c ON p.categoria_id = c.id
                       ORDER BY u.nombre";
$result_publicaciones = $conn_report->query($sql_publicaciones);
$num_publicaciones = $result_publicaciones ? $result_publicaciones->num_rows : 0;

//  biblioteca TCPDF 
require_once('tcpdf/tcpdf.php');

// funcion para generar el PDF de usuarios
function generarPDFUsuarios($result_usuarios) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Tu Nombre o Nombre de la Aplicación');
    $pdf->SetTitle('Reporte de Usuarios Registrados');
    $pdf->SetSubject('Reporte de Usuarios Registrados');
    $pdf->SetKeywords('reporte, usuarios, registrados');
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    $html = '<h1>Reporte de Usuarios Registrados</h1>';
    $html .= '<table border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Apellidos</th>';
    $html .= '<th>Género</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Cumpleaños</th>';
    $html .= '<th>Rol</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    if ($result_usuarios && $result_usuarios->num_rows > 0) {
        while ($row_usuario = $result_usuarios->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row_usuario['id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['apellidos']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['genero']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['email']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['cumpleanos']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_usuario['rol']) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="7">No hay datos de usuarios para mostrar en el PDF.</td></tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('reporte_usuarios.pdf', 'D'); // 
    exit(); // 
}

// 
function generarPDFPublicaciones($result_publicaciones) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Tu Nombre o Nombre de la Aplicación');
    $pdf->SetTitle('Reporte de Publicaciones Registradas');
    $pdf->SetSubject('Reporte de Publicaciones Registradas');
    $pdf->SetKeywords('reporte, publicaciones, registradas');
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetFont('helvetica', '', 8); // 
    $pdf->AddPage('L'); // 

    $html = '<h1>Reporte de Publicaciones Registradas</h1>';
    $html .= '<table border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Usuario</th>';
    $html .= '<th>Categoría</th>';
    $html .= '<th>Título</th>';
    $html .= '<th>Contenido</th>';
    $html .= '<th>Likes</th>';
    $html .= '<th>Tiempo de Creación</th>';
    $html .= '<th>Fecha Actualización</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    if ($result_publicaciones && $result_publicaciones->num_rows > 0) {
        while ($row_publicacion = $result_publicaciones->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['nombre_usuario']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['nombre_categoria']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['titulo']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['contenido']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['likes']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['tiempo_creacion']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row_publicacion['fecha_actualizacion']) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="8">No hay datos de publicaciones para mostrar en el PDF.</td></tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('reporte_publicaciones.pdf', 'D'); // 
    exit(); // 
}

// 
if (isset($_GET['descargar_pdf_usuarios'])) {
    if ($result_usuarios) {
        generarPDFUsuarios($result_usuarios);
    } else {
        echo '<p>No hay datos de usuarios para generar el PDF.</p>';
    }
}

// 
if (isset($_GET['descargar_pdf_publicaciones'])) {
    if ($result_publicaciones) {
        generarPDFPublicaciones($result_publicaciones);
    } else {
        echo '<p>No hay datos de publicaciones para generar el PDF.</p>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Usuarios y Publicaciones</title>
    <link rel="stylesheet" href="../css/reportes.css">
    <link rel="stylesheet" type="text/css" href="../css/reportes_dark.css" class="dark-mode-style" disabled>
    <style>
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-table th, .report-table td {
            border: 2px solid #aaa; 
            padding: 8px;
            text-align: left;
        }

        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .report-header {
            display: flex;
            align-items: center; 
            margin-bottom: 10px;
        }

        .report-header h3 {
            margin: 0;
            margin-right: 15px; 
        }

        .report-header span {
            font-size: 1.2em;
            font-weight: bold;
            color: #007bff;
        }

        .profile-image-report {
            width: 50px; 
            height: 50px;
            border-radius: 50%; 
            object-fit: cover; 
        }

        .pdf-button {
            padding: 8px 12px;
            background-color: #28a745; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            margin-left: 10px; 
        }

        .pdf-button:hover {
            background-color: #218838;
        }
    </style>
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
      
      
        <a href="reportes.php" class="nav-btn">Reportes</a>
      
      <a href="logout.php" class="nav-btn">Cerrar Sesión</a>
    <?php endif; ?>
  </div>
</header>

<div class="main-container" style="margin-top: 60px;">
    <div class="sidebar">
        <span><?= $nombreUsuario ?></span>
    </div>

    <div class="content">
        <div class="container">
            <div class="post">
                <div class="user-info">
                    </div>
                <div class="report-header">
                    <h3>Reportes de usuarios registrados <span style="margin-left: 10px;">Total: <?= $num_usuarios ?></span>
                        <?php if ($num_usuarios > 0): ?>
                            <a href="reportes.php?descargar_pdf_usuarios=true" class="pdf-button">Guardar en PDF</a>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="post-content">
                    <?php
                    if ($result_usuarios && $result_usuarios->num_rows > 0) {
                        echo '<table class="report-table">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>ID</th>';
                        echo '<th>Nombre</th>';
                        echo '<th>Apellidos</th>';
                        echo '<th>Género</th>';
                        echo '<th>Email</th>';
                        echo '<th>Cumpleaños</th>';
                        echo '<th>Rol</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        while ($row_usuario = $result_usuarios->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row_usuario['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['nombre']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['apellidos']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['genero']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['cumpleanos']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_usuario['rol']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No se encontraron usuarios registrados.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="post" style="margin-top: 30px;">
                <div class="user-info">
                    </div>
                <div class="report-header">
                    <h3>Reporte de publicaciones registradas <span style="margin-left: 10px;">Total: <?= $num_publicaciones ?></span>
                        <?php if ($num_publicaciones > 0): ?>
                            <a href="reportes.php?descargar_pdf_publicaciones=true" class="pdf-button">Guardar en PDF</a>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="post-content">
                    <?php
                    if ($result_publicaciones && $result_publicaciones->num_rows > 0) {
                        echo '<table class="report-table">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>ID</th>';
                        //echo '<th>Foto Perfil</th>';
                        echo '<th>Usuario</th>';
                        echo '<th>Categoría</th>';
                        echo '<th>Título</th>';
                        echo '<th>Contenido</th>';
                        echo '<th>Likes</th>';
                        echo '<th>Tiempo de Creación</th>';
                        echo '<th>Fecha Actualización</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        while ($row_publicacion = $result_publicaciones->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row_publicacion['id']) . '</td>';
                            /*echo '<td>';
                            if (!empty($row_publicacion['ruta_foto_usuario'])) {
                                echo '<img src="' . htmlspecialchars($row_publicacion['ruta_foto_usuario']) . '" alt="Foto de Perfil" class="profile-image-report">';
                            } else {
                                echo 'Sin foto';
                            }*/
                            echo '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['nombre_usuario']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['nombre_categoria']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['titulo']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['contenido']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['likes']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['tiempo_creacion']) . '</td>';
                            echo '<td>' . htmlspecialchars($row_publicacion['fecha_actualizacion']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No se encontraron publicaciones registradas.</p>';
                    }

                    $conn_report->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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

</body>
</html>