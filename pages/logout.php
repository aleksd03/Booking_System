<?php
session_start();

// Destroy all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page with message
session_start();
$_SESSION['success_message'] = "Успешно излязохте от системата!";

header('Location: ../index.php');
exit();
?>