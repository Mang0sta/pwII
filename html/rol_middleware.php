<?php
// rol_middleware.php

function requireRole($requiredRole) {
    //session_start();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $requiredRole) {
        // el usuario no tiene rol requerido para entrar 
        header("Location: index.php?error=forbidden&required_role=" . urlencode($requiredRole));
        exit();
    }
    
}

// 
function hasRole($roleToCheck) {
    session_start();
    return (isset($_SESSION['rol']) && $_SESSION['rol'] === $roleToCheck);
}

?>