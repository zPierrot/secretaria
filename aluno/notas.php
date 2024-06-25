<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'aluno') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student's grades
$sql = "SELECT turma.nome AS name, grades.grade 
        FROM grades 
        JOIN turma ON grades.turma_id = turma.id 
        WHERE grades.student_id = $user_id";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

$grades = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/notas_styles.css">
    <title>Consultar Notas</title>
</head>
<body>
    <div class="container">
        <h2>Consultar Notas</h2>
        <a href="index.php">Voltar</a>
        <h3>Suas Notas</h3>
        <ul>
            <?php foreach ($grades as $grade) : ?>
                <li><?php echo $grade['name']; ?>: <?php echo $grade['grade']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
