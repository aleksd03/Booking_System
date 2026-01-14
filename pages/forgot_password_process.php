<?php
session_start();
require_once '../config/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

// Get email
$email = trim($_POST['email']);

// Validate email
if (empty($email)) {
    $_SESSION['forgot_password_error'] = "Имейлът е задължителен";
    header('Location: forgot_password.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['forgot_password_error'] = "Невалиден имейл формат";
    header('Location: forgot_password.php');
    exit();
}

// Connect to database
$conn = getDbConnection();

// Check if email exists
$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
closeDbConnection($conn);

// Security note: Always show success message even if email doesn't exist
// This prevents email enumeration attacks
$_SESSION['forgot_password_message'] = "Ако този имейл адрес съществува в нашата система, вие ще получите инструкции за възстановяване на паролата. Моля проверете вашата входяща поща.";

header('Location: forgot_password.php');
exit();
?>