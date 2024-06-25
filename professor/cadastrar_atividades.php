<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $turma_id = $_POST['turma_id'];

    $sql = "INSERT INTO activities (description, turma_id) VALUES ('$description', $turma_id)";
    if ($conn->query($sql) === TRUE) {
        echo "Atividade cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar atividade: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Cadastrar Atividades</title>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Atividades</h2>
        <form method="post" action="cadastrar_atividades.php">
            <input type="hidden" name="turma_id" value="<?php echo $_GET['id']; ?>">
            <label for="description">Descrição da Atividade:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            <button type="submit">Cadastrar Atividade</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
