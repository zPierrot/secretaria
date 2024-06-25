<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <title>Registrar</title>
</head>
<body>
    <div class="register-container">
        <h2>Registrar</h2>
        <form method="post" action="register.php">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br>
            <label for="role">Função:</label>
            <select id="role" name="role" required>
                <option value="aluno">Criança</option>
                <option value="professor">Educador</option>
                <option value="secretaria">Secretário</option>
                <option value="admin">Administrador</option>
            </select><br>
            <button type="submit">Registrar</button>
        </form>
        <a href="login.php">Login</a>
    </div>
</body>
</html>
