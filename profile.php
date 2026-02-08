<?php
session_start();
require_once "config.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user
$stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | Crochet Haven</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f3f4f6;
    font-family: "Segoe UI", sans-serif;
}

.profile-wrapper {
    max-width: 420px;
    margin: 40px auto;
}

.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}

.profile-header {
    background: linear-gradient(135deg, #ff5f9e, #ff8fab);
    padding: 35px 20px 60px;
    text-align: center;
    color: white;
}

.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #fff;
    color: #ff5f9e;
    font-size: 36px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}

.profile-name {
    font-size: 20px;
    font-weight: 600;
}

.profile-email {
    font-size: 14px;
    opacity: 0.9;
}

.profile-body {
    padding: 25px;
}

.section-title {
    font-weight: 600;
    margin-bottom: 15px;
}

.form-control {
    border-radius: 10px;
}

.btn-save {
    background: #ff5f9e;
    border: none;
    padding: 10px;
    font-weight: 600;
    color: white;
}

.btn-save:hover {
    background: #e14f8d;
}

.link-box {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    text-decoration: none;
    color: #333;
}

.link-box:hover {
    color: #ff5f9e;
}
</style>
</head>

<body>

<div class="profile-wrapper">
    <div class="profile-card">

        <!-- HEADER -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?= strtoupper(substr(escapeOutput($user['full_name']), 0, 1)) ?>
            </div>

            <div class="profile-name">
                <?= escapeOutput($user['full_name']) ?>
            </div>

            <div class="profile-email">
                <?= escapeOutput($user['email']) ?>
            </div>
        </div>

        <!-- BODY -->
        <div class="profile-body">

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Profile updated successfully</div>
            <?php endif; ?>

            <h6 class="section-title">Edit Profile</h6>

            <form method="POST" action="update_profile.php">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                           value="<?= escapeOutput($user['full_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= escapeOutput($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= escapeOutput($user['phone']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password (optional)</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter new password">
                </div>

                <button class="btn btn-save w-100">Save Changes</button>
            </form>

            <hr>

            <a href="my_orders.php" class="link-box">
                <span>📦 My Orders</span>
                <span>›</span>
            </a>

            <a href="logout.php" class="link-box text-danger">
                <span>🚪 Logout</span>
                <span>›</span>
            </a>

        </div>
    </div>
</div>

</body>
</html>