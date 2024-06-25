<?php
session_start();
include '../config.php';

// Verificar se há uma sessão válida de professor/administrador
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'professor' && $_SESSION['role'] != 'admin')) {
    header("Location: ../login.php");
    exit();
}

// Verificar se foi enviado o formulário para selecionar a turma
$turma_id = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['turma_id'])) {
    $turma_id = $_POST['turma_id'];
}

// Se não houver turma selecionada via POST, tentar obter via GET
if (!$turma_id && isset($_GET['id']) && !empty($_GET['id'])) {
    $turma_id = $_GET['id'];
}

// Se ainda não tiver um ID de turma, exibir formulário para selecionar
if (!$turma_id) {
    // Consulta para obter todas as turmas do professor/administrador
    $sql_turmas = "SELECT turma.id, turma.nome AS turma, courses.name AS curso
                   FROM turma
                   INNER JOIN courses ON turma.curso_id = courses.id
                   WHERE turma.professor_id = ?";
    $stmt_turmas = $conn->prepare($sql_turmas);
    $stmt_turmas->bind_param("i", $_SESSION['user_id']);
    $stmt_turmas->execute();
    $result_turmas = $stmt_turmas->get_result();

    $turmas = [];
    if ($result_turmas->num_rows > 0) {
        while ($row = $result_turmas->fetch_assoc()) {
            $turmas[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Selecionar Turma e Aluno</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Selecionar Turma e Aluno</h2>
        
        <?php if (!$turma_id) : ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="turma">Selecione a Turma:</label>
                <select name="turma_id" id="turma">
                    <?php foreach ($turmas as $turma) : ?>
                        <option value="<?php echo htmlspecialchars($turma['id']); ?>"><?php echo htmlspecialchars($turma['curso'] . ' - ' . $turma['turma']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Visualizar Turma</button>
            </form>
        <?php else : ?>
            <?php
            // Consulta para obter informações da turma selecionada
            $sql_turma_info = "SELECT turma.nome AS turma, courses.name AS curso, users.name AS professor
                               FROM turma
                               INNER JOIN users ON turma.professor_id = users.id
                               INNER JOIN courses ON turma.curso_id = courses.id
                               WHERE turma.id = ?";
            $stmt_turma_info = $conn->prepare($sql_turma_info);
            $stmt_turma_info->bind_param("i", $turma_id);
            $stmt_turma_info->execute();
            $result_turma_info = $stmt_turma_info->get_result();

            if (!$result_turma_info || $result_turma_info->num_rows == 0) {
                echo "Turma não encontrada.";
                exit();
            }

            $turma_info = $result_turma_info->fetch_assoc();

            // Consulta para obter lista de crianças matriculadas na turma
            $sql_alunos = "SELECT users.id AS aluno_id, users.name AS aluno
                           FROM enrollments
                           INNER JOIN users ON enrollments.student_id = users.id
                           WHERE enrollments.turma_id = ?";
            $stmt_alunos = $conn->prepare($sql_alunos);
            $stmt_alunos->bind_param("i", $turma_id);
            $stmt_alunos->execute();
            $result_alunos = $stmt_alunos->get_result();

            $alunos = [];
            if ($result_alunos->num_rows > 0) {
                while ($row = $result_alunos->fetch_assoc()) {
                    $alunos[] = $row;
                }
            }
            ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="turma_id" value="<?php echo htmlspecialchars($turma_id); ?>">
                <label for="aluno">Selecione o Aluno:</label>
                <select name="aluno_id" id="aluno">
                    <?php foreach ($alunos as $aluno) : ?>
                        <option value="<?php echo htmlspecialchars($aluno['aluno_id']); ?>"><?php echo htmlspecialchars($aluno['aluno']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Visualizar Aluno</button>
            </form>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aluno_id'])) : ?>
                <?php
                $aluno_id = $_POST['aluno_id'];

                // Consulta para obter informações do aluno selecionado
                $sql_aluno_info = "SELECT users.name AS aluno
                                   FROM users
                                   WHERE users.id = ?";
                $stmt_aluno_info = $conn->prepare($sql_aluno_info);
                $stmt_aluno_info->bind_param("i", $aluno_id);
                $stmt_aluno_info->execute();
                $result_aluno_info = $stmt_aluno_info->get_result();

                if (!$result_aluno_info || $result_aluno_info->num_rows == 0) {
                    echo "Aluno não encontrado.";
                    exit();
                }

                $aluno_info = $result_aluno_info->fetch_assoc();

                // Consulta para obter notas do aluno na turma selecionada
                $sql_notas = "SELECT grade
                              FROM grades
                              WHERE student_id = ? AND turma_id = ?";
                $stmt_notas = $conn->prepare($sql_notas);
                $stmt_notas->bind_param("ii", $aluno_id, $turma_id);
                $stmt_notas->execute();
                $result_notas = $stmt_notas->get_result();

                $notas = [];
                if ($result_notas->num_rows > 0) {
                    while ($row = $result_notas->fetch_assoc()) {
                        $notas[] = $row['grade'];
                    }
                }
                ?>
                
                <h2>Notas do Aluno</h2>
                <p><strong>Aluno:</strong> <?php echo htmlspecialchars($aluno_info['aluno']); ?></p>
                <p><strong>Turma:</strong> <?php echo htmlspecialchars($turma_info['curso'] . ' - ' . $turma_info['turma']); ?></p>

                <h3>Notas</h3>
                <?php if (!empty($notas)) : ?>
                    <ul>
                        <?php foreach ($notas as $nota) : ?>
                            <li><?php echo htmlspecialchars($nota); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Nenhuma nota registrada para este aluno nesta turma.</p>
                <?php endif; ?>
                
                <a href="index.php">Voltar</a>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>
