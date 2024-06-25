<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'aluno') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datetime = $_POST['datetime']; // Deve corresponder ao nome do campo no formulário HTML
    $sql = "INSERT INTO appointments (student_id, datetime, status) VALUES ($user_id, '$datetime', 'pending')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Atendimento agendado com sucesso!";
    } else {
        echo "Erro ao agendar atendimento: " . $conn->error;
    }
}

// Fetch appointments
$sql = "SELECT * FROM appointments WHERE student_id = $user_id";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Agendar Atendimento</title>
</head>
<body>
    <div class="container">
        <h2>Agendar Atendimento</h2>
        <form method="post" action="atendimento.php">
            <label for="datetime">Data e Hora:</label>
            <input type="datetime-local" id="datetime" name="datetime" required>
            <button type="submit">Agendar</button>
        </form>
        <a href="index.php">Voltar</a>

        <h3>Atendimentos Agendados</h3>
        <ul>
            <?php foreach ($appointments as $appointment) : ?>
                <li><?php echo $appointment['datetime']; ?> - <?php echo $appointment['status']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
