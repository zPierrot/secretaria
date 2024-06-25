<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    $sql = "UPDATE users SET role = '$role' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "Informações do usuário atualizadas com sucesso!";
    } else {
        echo "Erro ao atualizar informações do usuário: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Gerenciar Usuários</title>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Usuários</h2>
        <form method="post" action="gerenciar_usuarios.php">
            <label for="user_id">Selecione o Usuário:</label>
            <select id="user_id" name="user_id" required>
                <?php foreach ($users as $user) : ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?> (<?php echo $user['role']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <label for="role">Novo Papel:</label>
            <select id="role" name="role" required>
                <option value="aluno">Criança</option>
                <option value="professor">Educador</option>
                <option value="secretaria">Secretário</option>
                <option value="admin">Administrador</option>
            </select>
            <button type="submit">Atualizar</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
