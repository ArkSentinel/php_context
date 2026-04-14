<?php
session_start(); // 1. Localizar la sesión actual

// 2. Limpiar todas las variables de sesión
$_SESSION = array();

// 3. Si se desea destruir la sesión completamente, 
// también se debe borrar la cookie de sesión del navegador.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión en el servidor
session_destroy();

// 5. Redirigir al login o a la página de inicio
header("Location: /index.php");
exit;
?>