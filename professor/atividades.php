<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}

// Fetch activities related to turma (classes) associated with the professor
$professor_id = $_SESSION['user_id'];
$sql = "SELECT activities.description, turma.nome
        FROM activities
        INNER JOIN turma ON activities.turma_id = turma.id
        WHERE turma.professor_id = $professor_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "Nenhuma atividade encontrada.";
    exit();
}

$activities = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Atividades</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Atividades</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Turma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity) : ?>
                    <tr>
                        <td><?php echo $activity['description']; ?></td>
                        <td><?php echo $activity['nome']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
