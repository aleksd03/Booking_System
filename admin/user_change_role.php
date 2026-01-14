<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get parameters
if (!isset($_GET['id']) || !isset($_GET['role'])) {
    $_SESSION['error_message'] = "Невалидни параметри";
    header('Location: users.php');
    exit();
}

$user_id = intval($_GET['id']);
$new_role = $_GET['role'];

// Validate role
if (!in_array($new_role, ['user', 'admin'])) {
    $_SESSION['error_message'] = "Невалидна роля";
    header('Location: users.php');
    exit();
}

// Prevent changing own role
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error_message'] = "Не можете да променяте собствената си роля";
    header('Location: users.php');
    exit();
}

// Update user role
$conn = getDbConnection();
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $new_role, $user_id);

if ($stmt->execute()) {
    $role_text = $new_role === 'admin' ? 'администратор' : 'потребител';
    $_SESSION['success_message'] = "Ролята беше успешно променена на '$role_text'";
} else {
    $_SESSION['error_message'] = "Грешка при промяна на ролята";
}

$stmt->close();
closeDbConnection($conn);

header('Location: users.php');
exit();
?>