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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <style>
        
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        .form-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            color: #333;
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
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-box button:hover {
            background-color: #0056b3;
        }

        .form-box .error-message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-box .success-message {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-box p {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .form-box p a {
            color: #007bff;
            text-decoration: none;
        }

        .form-box p a:hover {
            text-decoration: underline;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: blue;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            font-size: 20px;
            font-weight: bold;
            margin-left: 10px;
            color: white;
            text-decoration: none;
        }

        header div {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        header div a {
            color: white;
            text-decoration: none;
        }

        header div a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php">Social Link</a>
        <div>
            </div>
    </header>

    <div class="container" style="margin-top: 60px;">
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post" id="loginForm">
                <h2>Login</h2>
                <div id="login-message">
                    <?= showError($errors['login']); ?>
                </div>
                <input type="email" name="email" placeholder="Email"   required id="login-email">
                <input type="password" name="password" placeholder="Password"    required id="login-password">
                <button type="submit" name="login">Login</button>
                <p>No tienes una cuenta? <a href="#" onclick="showForm('register-form')">Regístrate</a></p>
            </form>
        </div>
        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <h2>Registrar</h2>
            <div id="register-message"></div>
            <input type="text" id="register-name" name="name" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Nombre"   required>
            <input type="text" id="register-apellidos" name="apellidos" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Apellidos" required>
            <select id="register-genero" name="genero" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" required>
                <option value="">--Género--</option>
                <option value="hombre">Hombre</option>
                <option value="mujer">Mujer</option>
            </select>
            <input type="email" id="register-email" name="email" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" placeholder="Email"   required>
            <input type="password" id="register-password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,40}$" oninvalid="this.setCustomValidity('La contraseña debe tener entre 8 y 40 caracteres y contener al menos una letra minúscula, una mayúscula, un número y un carácter especial.')" oninput="setCustomValidity('')" placeholder="Password" required>
            <input type="date" class="form-control" oninvalid="this.setCustomValidity('Favor de llenar este campo')" oninput="setCustomValidity('')" id="ID_FECHA_REGRISTRARSE.HTML"  name="cumpleanos"   required>
            <label for="register-foto">Foto de perfil:</label>
            <input type="file" id="register-foto" name="foto" accept="image/png, image/jpeg">
            <button type="button" onclick="registerUser()">Crear</button>
            <p>Ya tienes una cuenta? <a href="#" onclick="showForm('login-form')">Login</a></p>
        </div>
    </div>

    <script>
    function registerUser() {
        const name = document.getElementById('register-name').value;
        const apellidos = document.getElementById('register-apellidos').value;
        const genero = document.getElementById('register-genero').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const cumpleanos = document.getElementById('ID_FECHA_REGRISTRARSE.HTML').value;
        const fotoInput = document.getElementById('register-foto');
        const fotoFile = fotoInput.files[0]; // obtener el archivo seleccionado
        const messageDiv = document.getElementById('register-message');

        // validación de contraseña
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,40}$/;
        if (!passwordPattern.test(password)) {
            alert('La contraseña debe tener entre 8 y 40 caracteres y contener al menos una letra minúscula, una mayúscula, un número y un carácter especial.');
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('apellidos', apellidos);
        formData.append('genero', genero);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('cumpleanos', cumpleanos);

        if (fotoFile) {
            formData.append('foto', fotoFile); // 
            formData.append('fotoNombre', fotoFile.name); // 
        }

        fetch('api_register.php', {
            method: 'POST',
            body: formData // 
        })
        .then(response => response.json())
        .then(result => {
            messageDiv.innerHTML = '';
            if (result.error) {
                messageDiv.innerHTML = `<p class="error-message">${result.error}</p>`;
            } else if (result.message) {
                messageDiv.innerHTML = `<p class="success-message">${result.message}</p>`;
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            }
        })
        .catch(error => {
            messageDiv.innerHTML = `<p class="error-message">Error de red: ${error}</p>`;
        });
    }

    function showForm(formId) {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginForm.classList.remove('active');
        registerForm.classList.remove('active');

        document.getElementById(formId).classList.add('active');
    }

    
    document.addEventListener('DOMContentLoaded', function() {
        showForm('<?= $activeForm ?>-form');
    });
    </script>

    <script>
        var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yy = today.getFullYear();
    if(dd<10){
    dd='0'+dd
    }
    if(mm<10){
    mm='0'+mm
    } today = yy + '-' + mm + '-' + dd;
    document.getElementById("ID_FECHA_REGRISTRARSE.HTML").setAttribute("max", today);
    </script>

<script>
    function handleImageUpload()
    {
    var image = document.getElementById("register-foto").files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
        document.getElementById("display-image").src = e.target.result;
        }
        reader.readAsDataURL(image);
}
</script>

</body>
</html>