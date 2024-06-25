<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "provasecretaria";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function checkRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        header("Location: ../login.php");
        exit();
    }
}
?>
