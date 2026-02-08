<?php
session_start();
require_once "config.php";

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT id, created_at FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: my_orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Successful | Yarnora</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root { --primary:#ff9eb5; }

body {
    background:#f6f7fb;
    font-family:'Poppins',sans-serif;
}

.success-box {
    max-width:650px;
    margin:60px auto;
    background:#fff;
    padding:40px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.success-icon {
    font-size:4.5rem;
    color:#28a745;
}

.btn-primary {
    background:var(--primary);
    border:none;
    border-radius:10px;
    padding:10px 25px;
    font-weight:600;
}

.btn-outline-primary {
    border-radius:10px;
    padding:10px 25px;
    border-color:var(--primary);
    color:var(--primary);
}
.btn-outline-primary:hover {
    background:var(--primary);
    color:#fff;
}
</style>
</head>

<body>

<div class="container">
<div class="success-box">

    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>

    <h2 class="mb-2">Order Placed Successfully!</h2>

    <p class="text-muted mb-4">
        Thank you for shopping with <strong>Yarnora</strong> ❤️<br>
        Order ID: <strong>#<?= htmlspecialchars($orderId) ?></strong><br>
        Date: <?= date("d M Y, h:i A", strtotime($order['created_at'])) ?>
    </p>

    <p class="mb-4">
        You will receive an order confirmation soon.<br>
        You can check your order status anytime from your orders page.
    </p>

    <a href="index.php" class="btn btn-primary me-2">
        Continue Shopping
    </a>

    <a href="my_orders.php" class="btn btn-outline-primary">
        View My Orders
    </a>

</div>
</div>

<script>
localStorage.removeItem("cart");
</script>

</body>
</html>
