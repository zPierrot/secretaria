<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}

// Processo de exclusão de turma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $turma_id = $_POST['turma_id'];

    // Excluir matrículas associadas à turma
    $delete_enrollments_sql = "DELETE FROM enrollments WHERE turma_id = $turma_id";
    if ($conn->query($delete_enrollments_sql) === TRUE) {
        // Excluir atividades relacionadas à turma
        $delete_activities_sql = "DELETE FROM activities WHERE turma_id = $turma_id";
        if ($conn->query($delete_activities_sql) === TRUE) {
            // Excluir turma do banco de dados após excluir atividades
            $delete_turma_sql = "DELETE FROM turma WHERE id = $turma_id";
            if ($conn->query($delete_turma_sql) === TRUE) {
                echo "Turma, matrículas e atividades excluídas com sucesso!";
            } else {
                echo "Erro ao excluir turma: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir atividades: " . $conn->error;
        }
    } else {
        echo "Erro ao excluir matrículas: " . $conn->error;
    }
    exit(); // Termina o script após a exclusão
}

// Fetch professor's turmas
$professor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM turma WHERE professor_id = $professor_id";
$result = $conn->query($sql);

$turmas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $turmas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Excluir Turma</title>
</head>
<body>
    <div class="container">
        <h2>Excluir Turma</h2>
        <form method="post" action="turmas_excluir.php">
            <label for="turma">Selecione a Turma:</label>
            <select id="turma" name="turma_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma) : ?>
                    <option value="<?php echo $turma['id']; ?>"><?php echo $turma['nome']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Excluir</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
