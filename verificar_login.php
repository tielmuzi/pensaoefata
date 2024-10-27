<?php
session_start();

// Credenciais fixas (em um ambiente real, isso deveria estar em um banco de dados)
$usuario_correto = "admin";
$senha_correta = "admin123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $usuario_correto && $password === $senha_correta) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $username;
        header("Location: pedidos_admin.php");
        exit();
    } else {
        header("Location: login.html?erro=1");
        exit();
    }
}
?>