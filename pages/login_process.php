<?php
session_start();
require_once '../config/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Get form data
$email = trim($_POST['email']);
$password = $_POST['password'];

// Validation
$errors = [];

if (empty($email)) {
    $errors[] = "Имейлът е задължителен";
}

if (empty($password)) {
    $errors[] = "Паролата е задължителна";
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_email'] = $email;
    header('Location: login.php');
    exit();
}

// Connect to database
$conn = getDbConnection();

// Find user by email
$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found
    $_SESSION['login_errors'] = ["Невалиден имейл или парола"];
    $_SESSION['login_email'] = $email;
    $stmt->close();
    closeDbConnection($conn);
    header('Location: login.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
closeDbConnection($conn);

// Verify password
if (!password_verify($password, $user['password'])) {
    // Wrong password
    $_SESSION['login_errors'] = ["Невалиден имейл или парола"];
    $_SESSION['login_email'] = $email;
    header('Location: login.php');
    exit();
}

// Login successful - create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['success_message'] = "Успешно влязохте в системата!";

// Redirect based on role
if ($user['role'] === 'admin') {
    header('Location: ../admin/dashboard.php');
} else {
    header('Location: ../index.php');
}
exit();
?>