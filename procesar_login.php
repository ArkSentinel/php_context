<?php
session_start();
require 'conexion.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; 

    //1- Se busca al usuario
    $stmt = $pdo -> prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt ->execute([$email]);
    $user = $stmt ->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: crudequipos.php");
        exit;
    } else {
        echo "Email o contraseña incorrectos";
    }
}