<?php
session_start();
require_once "config.php";

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

// Get inputs
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

/* ================= VALIDATIONS ================= */

if ($name === '' || $email === '' || $phone === '' || $message === '') {
    redirectWithMessage("contact.php", "All fields are required.", "danger");
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage("contact.php", "Invalid email format.", "danger");
}

// Indian phone validation
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    redirectWithMessage("contact.php", "Please enter a valid 10-digit phone number.", "danger");
}

// Message length
if (strlen($message) < 5) {
    redirectWithMessage("contact.php", "Message must be at least 5 characters long.", "danger");
}

/* ================= INSERT INTO DATABASE ================= */

$stmt = $conn->prepare("
    INSERT INTO contact_messages (user_id, name, email, phone, message)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    redirectWithMessage("contact.php", "Database error. Please try again.", "danger");
}

// If user not logged in, bind NULL
$stmt->bind_param(
    "issss",
    $user_id,
    $name,
    $email,
    $phone,
    $message
);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: contact.php?success=1");
    exit;
} else {
    $stmt->close();
    redirectWithMessage("contact.php", "Something went wrong. Please try again.", "danger");
}