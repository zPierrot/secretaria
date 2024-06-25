<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'secretaria') {
    header("Location: ../login.php");
    exit();
}

// Fetch requested documents
$sql = "SELECT * FROM documents WHERE status = 'requested'";
$result = $conn->query($sql);
$documents = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $document_id = $_POST['document_id'];
    $target_dir = "uploads/";
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
            } else {
                echo "Erro ao emitir documento: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Emitir Documentos</title>
</head>
<body>
    <div class="container">
        <h2>Emitir Documentos</h2>
        <form method="post" action="emitir_documentos.php" enctype="multipart/form-data">
            <label for="document_id">Selecione o Documento:</label>
            <select id="document_id" name="document_id" required>
                <?php foreach ($documents as $document) : ?>
                    <option value="<?php echo $document['id']; ?>"><?php echo $document['type']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="file">Upload do Arquivo:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Emitir</button>
        </form>
        <a href="index.php">Voltar</a>

        <h3>Documentos Solicitados</h3>
        <ul>
            <?php foreach ($documents as $document) : ?>
                <li><?php echo $document['type']; ?> - <?php echo $document['status']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
