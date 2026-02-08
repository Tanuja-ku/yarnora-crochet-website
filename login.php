<?php
session_start();
require_once "config.php";
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$loginInput = trim($_POST['login_input'] ?? '');
$password   = $_POST['password'] ?? '';

if (empty($loginInput) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Please fill in all fields"]);
    exit;
}

// Fetch is_admin also
$sql = "SELECT id, full_name, email, phone, password, is_admin
        FROM users 
        WHERE email = ? OR phone = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query error"]);
    exit;
}

$stmt->bind_param("ss", $loginInput, $loginInput);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "message" => "Invalid email/phone or password"]);
    exit;
}

// Verify password
if (!verifyPassword($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid email/phone or password"]);
    exit;
}

// Store session
$_SESSION['user_id']   = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email']     = $user['email'];
$_SESSION['is_admin']  = $user['is_admin'];

// Send redirect info
if ($user['is_admin'] == 1) {
    echo json_encode([
        "success" => true,
        "message" => "Admin login successful!",
        "role" => "admin",
        "redirect" => "admin.php"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "message" => "Login successful!",
        "role" => "user",
        "redirect" => "index.php"
    ]);
}
?>
