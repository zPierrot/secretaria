<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'aluno') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Consulta para buscar informações da turma, curso e atividades do aluno logado
$sql = "SELECT courses.name AS nome_curso, turma.nome AS nome_turma, activities.description AS atividade
        FROM enrollments
        JOIN turma ON enrollments.turma_id = turma.id
        JOIN courses ON turma.curso_id = courses.id
        LEFT JOIN activities ON activities.turma_id = turma.id
        WHERE enrollments.student_id = $user_id";

$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

// Inicializar variáveis para armazenar os dados
$dados_turma = [];
$atividades = [];

// Processar os resultados da consulta
while ($row = $result->fetch_assoc()) {
    // Definir os dados do curso e turma
    $dados_turma['nome_curso'] = $row['nome_curso'];
    $dados_turma['nome_turma'] = $row['nome_turma'];

    // Adicionar atividades da turma, se existirem
    if ($row['atividade']) {
        $atividades[] = $row['atividade'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/criança_styles.css">
    <title>Painel do Aluno</title>
</head>
<body>
    <div class="container">
        <h2>Painel do Aluno</h2>
        <p>Bem-vindo, Aluno!</p>
        <a href="matricula.php">Matrícula em Cursos</a>
        <a href="notas.php">Consultar Notas</a>
        <a href="documentos.php">Solicitar Documentos</a>
        <a href="atendimento.php">Agendar Atendimento</a>
        <a href="../logout.php">Sair</a>

        <h3>Detalhes da Turma</h3>
        <p><strong>Curso:</strong> <?php echo isset($dados_turma['nome_curso']) ? $dados_turma['nome_curso'] : 'Não inscrito'; ?></p>
        <p><strong>Turma:</strong> <?php echo isset($dados_turma['nome_turma']) ? $dados_turma['nome_turma'] : 'Não inscrito'; ?></p>

        <h3>Atividades da Turma</h3>
        <ul>
            <?php if (!empty($atividades)) : ?>
                <?php foreach ($atividades as $atividade) : ?>
                    <li><?php echo $atividade; ?></li>
                <?php endforeach; ?>
            <?php else : ?>
                <li>Não há atividades cadastradas para esta turma.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
