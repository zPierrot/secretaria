<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'secretaria') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Painel da Secretaria</title>
</head>
<body>
    <div class="container">
        <h2>Painel da Secretaria</h2>
        <p>Bem-vindo, Secretário(a)!</p>
        <a href="emitir_documentos.php">Emitir Documentos</a><br>
        <a href="atualizar_cursos.php">Atualizar Informações de Cursos</a><br>
        <a href="../logout.php">Sair</a>
    </div>
</body>
</html>
