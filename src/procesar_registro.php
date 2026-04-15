<?php 
require_once 'Database.php';

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';
    
    $errors = [];

    if (empty($nombre)) {
        $errors[] = "El nombre es obligatorio";
    }
    if (empty($email)) {
        $errors[] = "El email es obligatorio";
    }
    if (empty($pass)) {
        $errors[] = "La contraseña es obligatoria";
    }
    if ($pass !== $pass2) {
        $errors[] = "Las contraseñas no coinciden";
    }
    if (strlen($pass) < 4) {
        $errors[] = "La contraseña debe tener al menos 4 caracteres";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        exit;
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
        
        header("Location: /login.php?registered=1");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "El correo ya está registrado.";
        } else {
            echo "Error al registrar: " . $e->getMessage();
        } 
    }
}