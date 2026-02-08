<?php
session_start();
require_once "config.php";

header("Content-Type: application/json");

// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Get & sanitize inputs
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$password  = trim($_POST['password'] ?? '');

// Validate required fields
if ($full_name === '' || $email === '' || $phone === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format"]);
    exit;
}

// Validate Indian phone number
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Invalid phone number"]);
    exit;
}

// Check if email or phone already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
if (!$check) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit;
}

$check->bind_param("ss", $email, $phone);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email or phone already registered"]);
    $check->close();
    exit;
}
$check->close();

// Hash password (helper from config.php)
$hashedPassword = hashPassword($password);

    // Insert user (force is_admin = 0)
$stmt = $conn->prepare("
    INSERT INTO users (full_name, email, phone, password, is_admin)
    VALUES (?, ?, ?, ?, 0)
");


if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit;
}

$stmt->bind_param("ssss", $full_name, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Registration successful! Please login."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Registration failed. Try again."
    ]);
}

$stmt->close();
?>