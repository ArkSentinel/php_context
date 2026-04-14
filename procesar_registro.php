<?php 
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];

    $email = $_POST['email'];
    $pass = $_POST['password'];
    $pass2 = $_POST['confirm_password'];
    
    if ($pass != $pass2) {
        die("Las contraseñas no coinciden. ");
    }

    if(strlen($pass) < 4) {
        die("La contraseña debe tener 4 caracteres");
    }

    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $passwordHash
        ]);
        echo "Uusario registrado con exito";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
        echo "El correo esta registrado previamente."; 
    } else {
        echo "Error al registrar: " . $e->getMessage();
        } 
    }
}

?>