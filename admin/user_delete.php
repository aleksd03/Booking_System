<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get user ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Невалиден потребител";
    header('Location: users.php');
    exit();
}

$user_id = intval($_GET['id']);

// Prevent deleting own account
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error_message'] = "Не можете да изтриете собствения си акаунт";
    header('Location: users.php');
    exit();
}

// Connect to database
$conn = getDbConnection();

// Get user name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Потребителят не съществува";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: users.php');
    exit();
}

$user_name = $result->fetch_assoc()['name'];
$stmt->close();

// Delete user's reservations first
$stmt = $conn->prepare("DELETE FROM reservations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Потребителят \"" . htmlspecialchars($user_name) . "\" и всички негови резервации бяха изтрити успешно";
} else {
    $_SESSION['error_message'] = "Грешка при изтриване на потребителя";
}

$stmt->close();
closeDbConnection($conn);

header('Location: users.php');
exit();
?>