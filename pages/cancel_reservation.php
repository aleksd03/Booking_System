<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Check if user is logged in
requireLogin();

// Get reservation ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Невалидна резервация";
    header('Location: my_reservations.php');
    exit();
}

$reservation_id = intval($_GET['id']);
$user_id = getUserId();

// Connect to database
$conn = getDbConnection();

// Check if reservation belongs to user and is cancellable
$stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Резервацията не съществува или нямате права да я откажете";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: my_reservations.php');
    exit();
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Check if reservation is already cancelled
if ($reservation['status'] === 'cancelled') {
    $_SESSION['error_message'] = "Тази резервация вече е отказана";
    closeDbConnection($conn);
    header('Location: my_reservations.php');
    exit();
}

// Check if reservation is in the past
$start_datetime = new DateTime($reservation['start_datetime']);
$now = new DateTime();

if ($start_datetime < $now) {
    $_SESSION['error_message'] = "Не можете да откажете минала резервация";
    closeDbConnection($conn);
    header('Location: my_reservations.php');
    exit();
}

// Cancel the reservation
$stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $reservation_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Резервацията беше успешно отказана";
} else {
    $_SESSION['error_message'] = "Грешка при отказване на резервацията. Моля опитайте отново";
}

$stmt->close();
closeDbConnection($conn);

header('Location: my_reservations.php');
exit();
?>