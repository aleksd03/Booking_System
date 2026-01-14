<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Check if user is logged in
requireLogin();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reservations.php');
    exit();
}

// Get form data
$resource_id = intval($_POST['resource_id']);
$reservation_date = trim($_POST['reservation_date']);
$start_time = trim($_POST['start_time']);
$end_time = trim($_POST['end_time']);
$notes = trim($_POST['notes']);
$user_id = getUserId();

// Validation
$errors = [];

// Validate resource
if (empty($resource_id)) {
    $errors[] = "Невалиден ресурс";
}

// Validate date
if (empty($reservation_date)) {
    $errors[] = "Датата е задължителна";
} else {
    $date_obj = DateTime::createFromFormat('Y-m-d', $reservation_date);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if (!$date_obj || $date_obj < $today) {
        $errors[] = "Датата трябва да е днес или в бъдещето";
    }
}

// Check if reservation is at least 3 hours in the future
if (!empty($reservation_date) && !empty($start_time)) {
    // Combine date and time
    $reservation_datetime_str = $reservation_date . ' ' . $start_time . ':00';
    $reservation_timestamp = strtotime($reservation_datetime_str);
    $current_timestamp = time();
    $min_required_timestamp = $current_timestamp + (3 * 60 * 60); // +3 hours
    
    $time_diff_hours = ($reservation_timestamp - $current_timestamp) / 3600;
    
    if ($reservation_timestamp <= $min_required_timestamp) {
        $hours_left = round($time_diff_hours, 1);
        $errors[] = "Резервацията трябва да бъде поне 3 часа в бъдещето. Вие сте избрали време след само {$hours_left} час/а от сега.";
    }
}

// Validate times
if (empty($start_time)) {
    $errors[] = "Началният час е задължителен";
}

if (empty($end_time)) {
    $errors[] = "Крайният час е задължителен";
}

if (!empty($start_time) && !empty($end_time)) {
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    
    if ($end <= $start) {
        $errors[] = "Крайният час трябва да е след началния час";
    }
    
    $duration_hours = ($end - $start) / 3600;
    
    if ($duration_hours > 24) {
        $errors[] = "Максималната продължителност е 24 часа";
    }
    
    if ($duration_hours < 0.5) {
        $errors[] = "Минималната продължителност е 30 минути";
    }
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['reservation_errors'] = $errors;
    $_SESSION['reservation_data'] = [
        'reservation_date' => $reservation_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'notes' => $notes
    ];
    header('Location: create_reservation.php?resource_id=' . $resource_id);
    exit();
}

// Connect to database
$conn = getDbConnection();

// Get resource details and check if available
$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['reservation_errors'] = ["Ресурсът не е наличен"];
    $_SESSION['reservation_data'] = [
        'reservation_date' => $reservation_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'notes' => $notes
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: create_reservation.php?resource_id=' . $resource_id);
    exit();
}

$resource = $result->fetch_assoc();
$stmt->close();

// Create datetime strings
$start_datetime = $reservation_date . ' ' . $start_time . ':00';
$end_datetime = $reservation_date . ' ' . $end_time . ':00';

// Check for overlapping reservations
$stmt = $conn->prepare("SELECT id FROM reservations 
                        WHERE resource_id = ? 
                        AND status IN ('pending', 'confirmed')
                        AND (
                            (start_datetime <= ? AND end_datetime > ?) OR
                            (start_datetime < ? AND end_datetime >= ?) OR
                            (start_datetime >= ? AND end_datetime <= ?)
                        )");
$stmt->bind_param("issssss", $resource_id, $start_datetime, $start_datetime, $end_datetime, $end_datetime, $start_datetime, $end_datetime);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['reservation_errors'] = ["Този ресурс вече е резервиран за избрания период. Моля изберете друг час или дата."];
    $_SESSION['reservation_data'] = [
        'reservation_date' => $reservation_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'notes' => $notes
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: create_reservation.php?resource_id=' . $resource_id);
    exit();
}
$stmt->close();

// Calculate total price
$start_timestamp = strtotime($start_time);
$end_timestamp = strtotime($end_time);
$duration_hours = ($end_timestamp - $start_timestamp) / 3600;
$total_price = $duration_hours * $resource['price_per_hour'];

// Insert reservation
$stmt = $conn->prepare("INSERT INTO reservations (user_id, resource_id, start_datetime, end_datetime, total_price, status, notes) 
                        VALUES (?, ?, ?, ?, ?, 'confirmed', ?)");
$stmt->bind_param("iissds", $user_id, $resource_id, $start_datetime, $end_datetime, $total_price, $notes);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Резервацията беше успешна! Вашата резервация е потвърдена.";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: my_reservations.php');
    exit();
} else {
    $_SESSION['reservation_errors'] = ["Грешка при създаването на резервацията. Моля опитайте отново."];
    $_SESSION['reservation_data'] = [
        'reservation_date' => $reservation_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'notes' => $notes
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: create_reservation.php?resource_id=' . $resource_id);
    exit();
}
?>