<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['active_form']);

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ?  'active' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium ⚽</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background:linear-gradient(to right, #CCFF99, #99FFCC, #b291eeff);
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #8b4efdff; /* Verde oscuro */
            padding: 12px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            font-size: 22px;
            font-weight: bold;
            color: #ffffffff;
            text-decoration: none;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('../img/stadium_bg.jpg') no-repeat center center/cover;
            padding-top: 80px;
        }

        .form-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #fbc02d;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 400px;
            margin: 20px;
            display: none;
        }

        .form-box.active {
            display: block;
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #004d40;
        }

        .form-box input[type=email],
        .form-box input[type=password],
        .form-box input[type=text],
        .form-box input[type=date],
        .form-box select,
        .form-box input[type=file] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-box button {
            width: 100%;
            padding: 10px;
            background-color: #fbc02d;
            color: #004d40;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .form-box button:hover {
            background-color: #c49000;
        }

        .form-box p {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .form-box p a {
            color: #004d40;
            text-decoration: none;
            font-weight: bold;
        }

        .form-box p a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
        }

        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
        }

        /* Banner */
        .banner {
            text-align: center;
            margin-top: 100px;
        }

        .banner img {
            width: 140px;
            margin-bottom: 10px;
        }

        .banner h2 {
            color: #004d40;
            font-size: 20px;
            font-weight: 600;
        }

        /* Balón animado */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .ball {
            width: 80px;
            height: 80px;
            background: url('../images/ball.png') no-repeat center/contain;
            animation: bounce 2s infinite;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<header>
    <a href="index.php">⚽ Stadium</a>
    <div></div>
</header>

<div class="banner">
    <img src="../img/worldcup_logo.png" alt="Mundial Logo">
    <h2>Conéctate con fanáticos del fútbol de todo el mundo 🌍⚽</h2>
    <div class="ball"></div>
</div>

<div class="container">
    <!-- Login -->
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post" id="loginForm">
            <h2>Inicia sesión en la pasión del Mundial</h2>
            <div id="login-message"><?= showError($errors['login']); ?></div>
            <input type="email" name="email" placeholder="Correo electrónico" required id="login-email">
            <input type="password" name="password" placeholder="Contraseña" required id="login-password">
            <button type="submit" name="login">Entrar al campo</button>
            <p>¿Aún no tienes cuenta? <a href="#" onclick="showForm('register-form')">Únete al equipo</a></p>
        </form>
    </div>

    <!-- Registro -->
    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
        <h2>Regístrate y únete al equipo</h2>
        <div id="register-message"></div>
        <input type="text" id="register-name" name="name" placeholder="Nombre" required>
        <input type="text" id="register-apellidos" name="apellidos" placeholder="Apellidos" required>
        <select id="register-genero" name="genero" required>
            <option value="">--Género--</option>
            <option value="hombre">Hombre</option>
            <option value="mujer">Mujer</option>
        </select>
        <input type="email" id="register-email" name="email" placeholder="Correo electrónico" required>
        <input type="password" id="register-password" name="password"
               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,40}$"
               placeholder="Contraseña" required>
        <input type="date" id="ID_FECHA_REGRISTRARSE.HTML" name="cumpleanos" required>
        <label for="register-foto">Foto de perfil:</label>
        <input type="file" id="register-foto" name="foto" accept="image/png, image/jpeg">
        <button type="button" onclick="registerUser()">Registrar jugador</button>
        <p>¿Ya tienes cuenta? <a href="#" onclick="showForm('login-form')">Volver al campo</a></p>
    </div>
</div>

<script>
function registerUser() {
    const formData = new FormData();
    formData.append('name', document.getElementById('register-name').value);
    formData.append('apellidos', document.getElementById('register-apellidos').value);
    formData.append('genero', document.getElementById('register-genero').value);
    formData.append('email', document.getElementById('register-email').value);
    formData.append('password', document.getElementById('register-password').value);
    formData.append('cumpleanos', document.getElementById('ID_FECHA_REGRISTRARSE.HTML').value);
    const foto = document.getElementById('register-foto').files[0];
    if (foto) formData.append('foto', foto);

    const messageDiv = document.getElementById('register-message');
    fetch('api_register.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        messageDiv.innerHTML = '';
        if (result.error) {
            messageDiv.innerHTML = `<p class="error-message">${result.error}</p>`;
        } else if (result.message) {
            messageDiv.innerHTML = `<p class="success-message">${result.message}</p>`;
            setTimeout(() => window.location.href = 'index.php', 1500);
        }
    })
    .catch(error => {
        messageDiv.innerHTML = `<p class="error-message">Error de red: ${error}</p>`;
    });
}

function showForm(formId) {
    document.getElementById('login-form').classList.remove('active');
    document.getElementById('register-form').classList.remove('active');
    document.getElementById(formId).classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    showForm('<?= $activeForm ?>-form');
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("ID_FECHA_REGRISTRARSE.HTML").setAttribute("max", today);
});
</script>

</body>
</html>
