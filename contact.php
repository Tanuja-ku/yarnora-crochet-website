<?php
session_start();
require_once "config.php";

// Fetch logged-in user details
$user = null;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$name = $user['full_name'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us | Crochet Haven</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary: #ff9eb5;
    --dark: #6d6875;
    --light: #fff1f4;
}
body {
    background: #f8f9fa;
    font-family: "Poppins", sans-serif;
}
.contact-container {
    max-width: 550px;
    margin: 60px auto;
    background: white;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}
.contact-header {
    text-align: center;
    margin-bottom: 25px;
}
.contact-header h3 {
    color: var(--primary);
    font-weight: 700;
}
.input-group-text {
    background: var(--light);
    border: none;
    color: var(--primary);
    font-size: 1.2rem;
}
.form-control {
    padding: 0.75rem;
    border-radius: 12px;
}
.btn-send {
    background: var(--primary);
    border: none;
    padding: 0.7rem;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    font-size: 1rem;
}
.btn-send:hover {
    background: #ff7b9d;
}
</style>
</head>

<body>

<div class="contact-container">

    <div class="contact-header">
        <h3><i class="fas fa-envelope-open-text me-2"></i>Contact Us</h3>
        <p class="text-muted">We’d love to hear from you! Fill out the form below.</p>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">🎉 Your message has been sent successfully!</div>
    <?php endif; ?>

    <form method="POST" action="submit_contact.php">

        <!-- Name -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Your Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($name) ?>" required>
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($email) ?>" required>
            </div>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Phone Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="tel" name="phone" class="form-control"
                       value="<?= htmlspecialchars($phone) ?>" placeholder="98xxxxxxxx" required>
            </div>
        </div>

        <!-- Message -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Message</label>
            <textarea name="message" rows="4" class="form-control"
                      placeholder="Write your message here..." required></textarea>
        </div>

        <button class="btn btn-send w-100">
            <i class="fas fa-paper-plane me-2"></i> Send Message
        </button>

    </form>

</div>

</body>
</html>