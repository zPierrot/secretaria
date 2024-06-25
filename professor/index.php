<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Painel do Educador</title>
</head>
<body>
    <div class="container">
        <h2>Painel do Educador</h2>
        <p>Bem-vindo, Educador!</p>
        <a href="cadastrar_turma.php">Cadastrar Turma</a><br>
        <a href="atividades.php">Mostrar Atividades</a><br>
        <a href="cadastrar_atividades.php">Lançar Atividade</a><br>
        <a href="turmas_excluir.php">Excluir Turma</a><br>
        <a href="adicionar_notas.php">Adicionar Notas</a><br>
        <a href="visualizar_turma.php?">Visualizar Crianças</a><br>
        <a href="../logout.php">Sair</a>
    </div>
</body>
</html>
