<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get resource ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Невалиден ресурс";
    header('Location: resources.php');
    exit();
}

$resource_id = intval($_GET['id']);

// Connect to database
$conn = getDbConnection();

// Check if resource exists
$stmt = $conn->prepare("SELECT name FROM resources WHERE id = ?");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Ресурсът не съществува";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: resources.php');
    exit();
}

$resource_name = $result->fetch_assoc()['name'];
$stmt->close();

// Delete all reservations for this resource first
$stmt = $conn->prepare("DELETE FROM reservations WHERE resource_id = ?");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$stmt->close();

// Delete the resource
$stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
$stmt->bind_param("i", $resource_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Ресурсът \"" . htmlspecialchars($resource_name) . "\" и всички негови резервации бяха изтрити успешно!";
} else {
    $_SESSION['error_message'] = "Грешка при изтриване на ресурса. Моля опитайте отново.";
}

$stmt->close();
closeDbConnection($conn);

header('Location: resources.php');
exit();
?>