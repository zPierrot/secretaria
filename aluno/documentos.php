<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'aluno') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Processamento para solicitar documento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'solicitar_documento') {
    $type = $_POST['type'];
    $sql = "INSERT INTO documents (student_id, type, status) VALUES ($user_id, '$type', 'requested')";

    if ($conn->query($sql) === TRUE) {
        echo "Documento solicitado com sucesso!";
        // Redirecionar para evitar reenvios de formulário
        header("Location: documentos.php");
        exit();
    } else {
        echo "Erro ao solicitar documento: " . $conn->error;
    }
}

// Processamento para remover arquivo anexado ao documento emitido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'remover_arquivo') {
    $remove_file_document_id = $_POST['remove_file_document_id'];

    // Verificar se o documento pertence ao aluno atual
    $sql_check = "SELECT * FROM documents WHERE id = $remove_file_document_id AND student_id = $user_id";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Atualizar o caminho do arquivo no banco de dados para vazio
        $sql_update = "UPDATE documents SET file_path = NULL WHERE id = $remove_file_document_id";
        if ($conn->query($sql_update) === TRUE) {
            echo "Arquivo removido com sucesso!";
            // Redirecionar para evitar reenvios de formulário
            header("Location: documentos.php");
            exit();
        } else {
            echo "Erro ao remover arquivo: " . $conn->error;
        }
    } else {
        echo "Você não tem permissão para remover o arquivo deste documento.";
    }
}

// Processamento para emitir documento (upload de arquivo)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'emitir_documento') {
    $document_id = $_POST['document_id'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual document
    $check = filesize($_FILES["file"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not a document.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["file"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC & DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $sql = "UPDATE documents SET status = 'issued', file_path = '$target_file' WHERE id = $document_id";
            if ($conn->query($sql) === TRUE) {
                echo "Documento emitido com sucesso!";
                // Redirecionar para evitar reenvios de formulário
                header("Location: documentos.php");
                exit();
            } else {
                echo "Erro ao emitir documento: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch documentos emitidos para o aluno
$sql = "SELECT * FROM documents WHERE student_id = $user_id AND status = 'issued'";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

$documents = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/documentos_styles.css">
    <title>Meus Documentos</title>
</head>
<body>
    <div class="container">
        <h2>Meus Documentos</h2>

        <h3>Solicitar Novo Documento</h3>
        <form method="post" action="documentos.php">
            <input type="hidden" name="action" value="solicitar_documento">
            <label for="type">Tipo de Documento:</label>
            <select id="type" name="type" required>
                <option value="declaração de matrícula">Declaração de Matrícula</option>
                <option value="histórico escolar">Histórico Escolar</option>
            </select>
            <button type="submit">Solicitar</button>
        </form>

        <h3>Documentos Emitidos</h3>
        <ul>
            <?php foreach ($documents as $document) : ?>
                <li>
                    <?php echo $document['type']; ?> - Emitido
                    <?php if (!empty($document['file_path'])) : ?>
                        <a href="<?php echo $document['file_path']; ?>" target="_blank">Ver Documento</a>
                        <form method="post" action="documentos.php">
                            <input type="hidden" name="action" value="remover_arquivo">
                            <input type="hidden" name="remove_file_document_id" value="<?php echo $document['id']; ?>">
                            <button type="submit">Remover Arquivo</button>
                        </form>
                    <?php else: ?>
                        <span>Sem arquivo anexado</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
