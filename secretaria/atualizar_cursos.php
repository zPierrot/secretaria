<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'secretaria') {
    header("Location: ../login.php");
    exit();
}

// Fetch all courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
$courses = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $name = $_POST['name'];
    $sql = "UPDATE courses SET name = '$name' WHERE id = $course_id";
    if ($conn->query($sql) === TRUE) {
        echo "Informações do curso atualizadas com sucesso!";
    } else {
        echo "Erro ao atualizar informações do curso: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Atualizar Informações de Cursos</title>
</head>
<body>
    <div class="container">
        <h2>Atualizar Informações de Cursos</h2>
        <form method="post" action="atualizar_cursos.php">
            <label for="course_id">Selecione o Curso:</label>
            <select id="course_id" name="course_id" required>
                <?php foreach ($courses as $course) : ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="name">Novo Nome do Curso:</label>
            <input type="text" id="name" name="name" required>
            <button type="submit">Atualizar</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
