<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: resources.php');
    exit();
}

// Get form data
$name = trim($_POST['name']);
$category_id = intval($_POST['category_id']);
$description = trim($_POST['description']);
$capacity = !empty($_POST['capacity']) ? intval($_POST['capacity']) : null;
$price_per_hour = floatval($_POST['price_per_hour']);
$location = trim($_POST['location']);
$status = $_POST['status'];

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Името е задължително";
} elseif (strlen($name) < 3) {
    $errors[] = "Името трябва да е поне 3 символа";
}

if (empty($category_id)) {
    $errors[] = "Категорията е задължителна";
}

if (empty($description)) {
    $errors[] = "Описанието е задължително";
} elseif (strlen($description) < 10) {
    $errors[] = "Описанието трябва да е поне 10 символа";
}

if ($price_per_hour <= 0) {
    $errors[] = "Цената трябва да е положително число";
}

if (empty($location)) {
    $errors[] = "Локацията е задължителна";
}

if (!in_array($status, ['available', 'unavailable'])) {
    $errors[] = "Невалиден статус";
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['resource_errors'] = $errors;
    $_SESSION['resource_data'] = [
        'name' => $name,
        'category_id' => $category_id,
        'description' => $description,
        'capacity' => $capacity,
        'price_per_hour' => $price_per_hour,
        'location' => $location,
        'status' => $status
    ];
    header('Location: resource_add.php');
    exit();
}

// Connect to database
$conn = getDbConnection();

// Check if resource with same name exists
$stmt = $conn->prepare("SELECT id FROM resources WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['resource_errors'] = ["Ресурс с това име вече съществува"];
    $_SESSION['resource_data'] = [
        'name' => $name,
        'category_id' => $category_id,
        'description' => $description,
        'capacity' => $capacity,
        'price_per_hour' => $price_per_hour,
        'location' => $location,
        'status' => $status
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: resource_add.php');
    exit();
}
$stmt->close();

// Insert new resource
$stmt = $conn->prepare("INSERT INTO resources (category_id, name, description, capacity, price_per_hour, location, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issidss", $category_id, $name, $description, $capacity, $price_per_hour, $location, $status);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Ресурсът беше успешно добавен!";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: resources.php');
    exit();
} else {
    $_SESSION['resource_errors'] = ["Грешка при добавяне на ресурса. Моля опитайте отново."];
    $_SESSION['resource_data'] = [
        'name' => $name,
        'category_id' => $category_id,
        'description' => $description,
        'capacity' => $capacity,
        'price_per_hour' => $price_per_hour,
        'location' => $location,
        'status' => $status
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: resource_add.php');
    exit();
}
?>