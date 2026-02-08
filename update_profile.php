<?php
session_start();
require_once "config.php";

/* Ensure user is logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Collect input */
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$password  = trim($_POST['password'] ?? '');

/* ================= VALIDATION ================= */

if ($full_name === '' || $email === '' || $phone === '') {
    redirectWithMessage("profile.php", "All fields except password are required.", "danger");
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage("profile.php", "Invalid email format.", "danger");
}

// Indian phone validation
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    redirectWithMessage("profile.php", "Invalid phone number.", "danger");
}

// Password validation (only if provided)
if ($password !== '' && strlen($password) < 6) {
    redirectWithMessage("profile.php", "Password must be at least 6 characters.", "danger");
}

/* ================= DUPLICATE CHECK ================= */

// Check email already exists for another user
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
if (!$checkEmail) {
    redirectWithMessage("profile.php", "Database error.", "danger");
}
$checkEmail->bind_param("si", $email, $user_id);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    $checkEmail->close();
    redirectWithMessage("profile.php", "Email already in use.", "danger");
}
$checkEmail->close();

// Check phone already exists for another user
$checkPhone = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
if (!$checkPhone) {
    redirectWithMessage("profile.php", "Database error.", "danger");
}
$checkPhone->bind_param("si", $phone, $user_id);
$checkPhone->execute();
$checkPhone->store_result();

if ($checkPhone->num_rows > 0) {
    $checkPhone->close();
    redirectWithMessage("profile.php", "Phone number already in use.", "danger");
}
$checkPhone->close();

/* ================= UPDATE USER ================= */

if ($password !== '') {
    // Hash password using helper
    $hashed = hashPassword($password);

    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone = ?, password = ?
        WHERE id = ?
    ");
    if (!$stmt) {
        redirectWithMessage("profile.php", "Database error.", "danger");
    }

    $stmt->bind_param("ssssi", $full_name, $email, $phone, $hashed, $user_id);

} else {

    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone = ?
        WHERE id = ?
    ");
    if (!$stmt) {
        redirectWithMessage("profile.php", "Database error.", "danger");
    }

    $stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);
}

/* Execute update */
if (!$stmt->execute()) {
    $stmt->close();
    redirectWithMessage("profile.php", "Profile update failed.", "danger");
}

$stmt->close();

/* Update session data */
$_SESSION['full_name'] = $full_name;

/* Redirect success */
redirectWithMessage("profile.php?success=1", "Profile updated successfully.", "success");