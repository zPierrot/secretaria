<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'aluno') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch available courses
$sql_courses = "SELECT * FROM courses";
$result_courses = $conn->query($sql_courses);
$courses = $result_courses->fetch_all(MYSQLI_ASSOC);

// Fetch available turmas (classes)
$sql_turmas = "SELECT turma.id, turma.nome, turma.curso_id, courses.name AS nome_curso
               FROM turma
               JOIN courses ON turma.curso_id = courses.id";
$result_turmas = $conn->query($sql_turmas);
$turmas = $result_turmas->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['turma_id']) && isset($_POST['course_id'])) {
        $turma_id = $_POST['turma_id'];
        $course_id = $_POST['course_id'];

        // Check if the selected turma belongs to the selected course
        $sql_check = "SELECT * FROM turma WHERE id = $turma_id AND curso_id = $course_id";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // Insert enrollment into database
            $sql_insert = "INSERT INTO enrollments (student_id, turma_id) VALUES ($user_id, $turma_id)";

            if ($conn->query($sql_insert) === TRUE) {
                echo "Matrícula realizada com sucesso!";
            } else {
                echo "Erro ao matricular: " . $conn->error;
            }
        } else {
            echo "Erro: Turma não pertence ao curso selecionado.";
        }
    } else {
        echo "Erro: Curso ou Turma não selecionados.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/matricula_styles.css">
    <title>Matrícula em Cursos</title>
</head>
<body>
    <div class="container">
        <h2>Matrícula em Cursos</h2>
        <form method="post" action="matricula.php">
            <label for="course_id">Selecione o Curso:</label>
            <select id="course_id" name="course_id" required>
                <?php foreach ($courses as $course) : ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="turma_id">Selecione a Turma:</label>
            <select id="turma_id" name="turma_id" required>
                <?php foreach ($turmas as $turma) : ?>
                    <option value="<?php echo $turma['id']; ?>" data-curso="<?php echo $turma['curso_id']; ?>"><?php echo $turma['nome']; ?> (<?php echo $turma['nome_curso']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Matricular</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
    <script>
        // JavaScript to filter turmas based on selected course
        document.getElementById('course_id').addEventListener('change', function () {
            var course_id = this.value;
            var turmaSelect = document.getElementById('turma_id');
            for (var i = 0; i < turmaSelect.options.length; i++) {
                var option = turmaSelect.options[i];
                if (option.getAttribute('data-curso') == course_id) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });

        // Trigger the change event on page load to filter the turmas based on the selected course
        document.getElementById('course_id').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
