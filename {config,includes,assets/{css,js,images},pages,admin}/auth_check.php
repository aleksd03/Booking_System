<?php
// Authentication check helper
// Include this file on pages that require login

if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user name
function getUserName() {
    return $_SESSION['user_name'] ?? 'Guest';
}

// Get current user email
function getUserEmail() {
    return $_SESSION['user_email'] ?? '';
}

// Get current user role
function getUserRole() {
    return $_SESSION['user_role'] ?? 'user';
}

// Check if user is admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Require login - redirect to login page if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /booking-system/pages/login.php');
        exit();
    }
}

// Require admin - redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error_message'] = "Нямате достъп до тази страница";
        header('Location: /booking-system/index.php');
        exit();
    }
}
?>