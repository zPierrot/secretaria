<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepara a consulta SQL
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifica se a senha é correta
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'aluno':
                    header("Location: aluno/index.php");
                    break;
                case 'professor':
                    header("Location: professor/index.php");
                    break;
                case 'secretaria':
                    header("Location: secretaria/index.php");
                    break;
                case 'admin':
                    header("Location: admin/index.php");
                    break;
            }
            exit();
        } else {
            $error_message = "Email ou senha inválidos.";
        }
    } else {
        $error_message = "Email ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/styles.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>
        <form method="post" action="login.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Entrar</button>
        </form>
        <a href="register.php">Registrar</a>
    </div>
</body>
</html>
