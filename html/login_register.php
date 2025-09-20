<?php

session_start();
require_once 'database.php'; // 

// 
$database = new Database();

// 
$conn = $database->getConnection();

if (!$conn) {
    // 
    error_log("Error al obtener la conexión a la base de datos en login_register.php.");
    $_SESSION['register_error'] = 'Error interno al registrar el usuario.';
    $_SESSION['active_form'] = 'register';
    header("Location: index.php");
    exit();
}

if (isset($_POST['register'])){
    $name = $_POST['name'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $cumpleanos = $_POST['cumpleanos'] ?? '';

    $checkEmail = $conn->prepare("SELECT email FROM a WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'El email ya está registrado';
        $_SESSION['active_form'] = 'register';
    } else {
        $stmt = $conn->prepare("INSERT INTO a (nombre, apellidos, genero, email, password, cumpleanos) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $apellidos, $genero, $email, $password, $cumpleanos); // Asumo que 'password' es VARCHAR
        if ($stmt->execute()) {
            // 
        } else {
            $_SESSION['register_error'] = 'Error al registrar el usuario.';
            $_SESSION['active_form'] = 'register';
            error_log("Error al ejecutar la consulta de registro: " . $stmt->error);
        }
        $stmt->close();
    }
    $checkEmail->close();
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM a WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if (($password === $user['password'])){
            echo "Contraseña coincide<br>"; //
            $_SESSION['user_id_mysql'] = (int) $user['id']; // Casteo a entero
            echo "User ID seteado en sesión: " . $_SESSION['user_id_mysql'] . "<br>"; // 
            // 
            $_SESSION['name'] = $user['nombre'];
            $_SESSION['apellidos'] = $user['apellidos'];
            $_SESSION['genero'] = $user['genero'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['cumpleanos'] = $user['cumpleanos'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] === 'admin'){
                header("Location: reportes.php");
            }  else {
                header("Location: publicacion.php"); // redirigir a publicacion.php
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Correo o contraseña incorrectos';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

$database->closeConnection(); // 
?>