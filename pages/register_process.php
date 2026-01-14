<?php
session_start();
require_once '../config/database.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// Get form data and sanitize
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validation
$errors = [];

// Validate name
if (empty($name)) {
    $errors[] = "Името е задължително";
} elseif (strlen($name) < 3) {
    $errors[] = "Името трябва да е поне 3 символа";
} else {
    // Check if name contains at least first name and last name
    $name_parts = explode(' ', trim($name));
    $name_parts = array_filter($name_parts); // Remove empty elements
    
    if (count($name_parts) < 2) {
        $errors[] = "Моля въведете пълно име (име и фамилия)";
    }
}

// Validate email
if (empty($email)) {
    $errors[] = "Имейлът е задължителен";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Невалиден имейл формат";
}

// Validate phone
if (empty($phone)) {
    $errors[] = "Телефонът е задължителен";
} elseif (strlen($phone) < 10) {
    $errors[] = "Телефонът трябва да е поне 10 цифри";
}

// Validate password
if (empty($password)) {
    $errors[] = "Паролата е задължителна";
} elseif (strlen($password) < 6) {
    $errors[] = "Паролата трябва да е поне 6 символа";
}

// Check if passwords match
if ($password !== $confirm_password) {
    $errors[] = "Паролите не съвпадат";
}

// If there are validation errors, redirect back with errors
if (!empty($errors)) {
    $_SESSION['registration_errors'] = $errors;
    $_SESSION['registration_data'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ];
    header('Location: register.php');
    exit();
}

// Connect to database
$conn = getDbConnection();

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['registration_errors'] = ["Този имейл вече е регистриран"];
    $_SESSION['registration_data'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ];
    $stmt->close();
    closeDbConnection($conn);
    header('Location: register.php');
    exit();
}
$stmt->close();

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

if ($stmt->execute()) {
    // Registration successful
    $user_id = $conn->insert_id;
    
    // Log the user in automatically
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = 'user';
    $_SESSION['success_message'] = "Регистрацията беше успешна! Добре дошли в ADBook!";
    
    $stmt->close();
    closeDbConnection($conn);
    
    // Redirect to home page
    header('Location: ../index.php');
    exit();
} else {
    // Registration failed
    $_SESSION['registration_errors'] = ["Грешка при регистрацията. Моля опитайте отново."];
    $_SESSION['registration_data'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ];
    
    $stmt->close();
    closeDbConnection($conn);
    
    header('Location: register.php');
    exit();
}
?>