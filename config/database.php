<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'DeathlyHallows666!');
define('DB_NAME', 'booking_system');

// Create connection
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8 for Bulgarian language support
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Close connection
function closeDbConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
