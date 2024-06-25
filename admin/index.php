<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Painel do Administrador</title>
</head>
<body>
    <div class="container">
        <h2>Painel do Administrador</h2>
        <p>Bem-vindo, Administrador!</p>
        <a href="gerenciar_usuarios.php">Gerenciar Usuários</a><br>
        <a href="gerenciar_cursos.php">Gerenciar Cursos</a><br>
        <a href="../logout.php">Sair</a>
    </div>
</body>
</html>
