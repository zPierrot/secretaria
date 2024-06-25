<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
$courses = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $sql = "INSERT INTO courses (name) VALUES ('$name')";
        if ($conn->query($sql) === TRUE) {
            echo "Curso adicionado com sucesso!";
        } else {
            echo "Erro ao adicionar curso: " . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $course_id = $_POST['course_id'];
        $sql = "DELETE FROM courses WHERE id = $course_id";
        if ($conn->query($sql) === TRUE) {
            echo "Curso excluído com sucesso!";
        } else {
            echo "Erro ao excluir curso: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Gerenciar Cursos</title>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Cursos</h2>
        <form method="post" action="gerenciar_cursos.php">
            <label for="name">Nome do Curso:</label>
            <input type="text" id="name" name="name" required>
            <button type="submit" name="add">Adicionar Curso</button>
        </form>

        <h3>Cursos Existentes</h3>
        <ul>
            <?php foreach ($courses as $course) : ?>
                <li>
                    <?php echo $course['name']; ?>
                    <form method="post" action="gerenciar_cursos.php" style="display:inline;">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <button type="submit" name="delete">Excluir</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
