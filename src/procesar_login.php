<?php
session_start();
ob_start();
require_once __DIR__ . '/Database.php';

$pdo = Database::getInstance()->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; 

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        ob_end_clean();
        header("Location: /admin/equipos.php");
        exit;
    } else {
        echo "Email o contraseña incorrectos";
    }
}