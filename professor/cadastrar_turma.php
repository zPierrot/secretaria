<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: ../login.php");
    exit();
}

$professor_id = $_SESSION['user_id'];

// Fetch available courses
$sql_courses = "SELECT * FROM courses";
$result_courses = $conn->query($sql_courses);
$courses = $result_courses->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $course_id = $_POST['course_id'];
    $sql = "INSERT INTO turma (nome, professor_id, curso_id) VALUES ('$name', $professor_id, $course_id)";
    if ($conn->query($sql) === TRUE) {
        // Obter o ID da turma recém-cadastrada
        $turma_id = $conn->insert_id;

        // Redirecionar para página de cadastrar atividades
        header("Location: cadastrar_atividades.php?id=$turma_id");
        exit();
    } else {
        echo "Erro ao cadastrar turma: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Cadastrar Turma</title>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Turma</h2>
        <form method="post" action="cadastrar_turma.php">
            <label for="name">Nome da Turma:</label>
            <input type="text" id="name" name="name" required>
            <label for="course_id">Curso:</label>
            <select id="course_id" name="course_id" required>
                <?php foreach ($courses as $course) : ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Cadastrar</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
