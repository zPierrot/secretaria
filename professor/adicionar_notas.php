<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $turma_id = $_POST['turma_id'];
    $grade = $_POST['grade'];

    // Inserir ou atualizar a nota do aluno
    $sql = "INSERT INTO grades (student_id, turma_id, grade) VALUES ($student_id, $turma_id, $grade)
            ON DUPLICATE KEY UPDATE grade = $grade";
    
    if ($conn->query($sql) === TRUE) {
        echo "Nota adicionada com sucesso!";
    } else {
        echo "Erro ao adicionar nota: " . $conn->error;
    }
}

// Fetch all students
$sql_students = "SELECT * FROM users WHERE role = 'aluno'";
$result_students = $conn->query($sql_students);
$students = $result_students->fetch_all(MYSQLI_ASSOC);

// Fetch all turmas
$sql_turmas = "SELECT * FROM turma";
$result_turmas = $conn->query($sql_turmas);
$turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Adicionar Notas</title>
</head>
<body>
    <div class="container">
        <h2>Adicionar Notas</h2>
        <form method="post" action="adicionar_notas.php">
            <label for="student_id">Selecione o Aluno:</label>
            <select id="student_id" name="student_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($students as $student) : ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="turma_id">Selecione a Turma:</label>
            <select id="turma_id" name="turma_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma) : ?>
                    <option value="<?php echo $turma['id']; ?>"><?php echo $turma['nome']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="grade">Nota (0-10):</label>
            <input type="number" id="grade" name="grade" min="0" max="10" step="0.1" required>

            <button type="submit">Adicionar Nota</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
