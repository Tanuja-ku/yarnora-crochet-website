<?php
// config.php

// Error reporting: Display errors off in production, log them
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for development
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log'); // Ensure /logs directory exists and is writable

// Timezone
date_default_timezone_set("Asia/Kolkata");

// Database constants (Update these for production)
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", ""); // Use environment variables or .env for security in production
define("DB_NAME", "crochet_haven");

// MySQLi connection with error handling
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later."); // More user-friendly message
}

mysqli_set_charset($conn, "utf8mb4");

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Password helpers
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Escape output for HTML (NOT for SQL - use prepared statements for SQL)
function escapeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Flash redirect helper
function redirectWithMessage($location, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $location");
    exit();
}

// Optional: Close connection at script end (though PHP does this automatically)
register_shutdown_function(function() use ($conn) {
    $conn->close();
});