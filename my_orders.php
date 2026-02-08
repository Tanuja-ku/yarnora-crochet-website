<?php
session_start();
require_once "config.php";
require_once "products.php"; // load products from array

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ================= NORMAL ORDERS ================= */
$sql = "
SELECT 
    o.id AS order_id,
    o.status,
    o.created_at,
    oi.quantity,
    oi.price,
    oi.product_id
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.user_id = ?
ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {

    // Find product name from products array
    $productName = "Unknown Product";
    foreach ($products as $p) {
        if ($p['id'] == $row['product_id']) {
            $productName = $p['name'];
            break;
        }
    }

    $row['product_name'] = $productName;
    $orders[$row['order_id']][] = $row;
}
$stmt->close();

/* ================= CUSTOM ORDERS ================= */
$customQuery = "SELECT * FROM custom_orders WHERE user_id = ? ORDER BY created_at DESC";
$customStmt = $conn->prepare($customQuery);
$customStmt->bind_param("i", $user_id);
$customStmt->execute();
$customOrders = $customStmt->get_result();
$customStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders | Crochet Haven</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --primary-color: #ff9eb5;
    --secondary-color: #a8e6cf;
    --light: #fff9f4;
    --dark: #6d6875;
}
body {
    background: #f8f9fa;
    font-family: 'Poppins', sans-serif;
}
.order-box {
    background: white;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.badge-status {
    padding: 6px 10px;
    font-size: 0.8rem;
    border-radius: 6px;
}
.badge-pending { background: #ffce54; color:#000; }
.badge-processing { background:#4fc1e9; }
.badge-completed { background:#a0d468; }
.badge-cancelled { background:#ed5565; }
.section-title {
    font-weight: 600;
    margin-top: 40px;
    color: var(--dark);
}
.table th {
    background: #fff1f4;
}
</style>
</head>

<body>
<div class="container mt-4">

<h2 class="text-center mb-4" style="color:var(--primary-color); font-weight:700;">
    My Orders
</h2>

<!-- NORMAL ORDERS -->
<h4 class="section-title">🛒 Normal Orders</h4>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">You have not placed any normal orders yet.</div>
<?php else: ?>

<?php foreach ($orders as $order_id => $items): ?>
<div class="order-box">

<div class="d-flex justify-content-between">
<strong>Order #<?= htmlspecialchars($order_id) ?></strong>

<?php
$status = strtolower($items[0]['status']);
switch ($status) {
    case "pending":
        $badgeClass = "badge-pending";
        break;
    case "processing":
        $badgeClass = "badge-processing";
        break;
    case "completed":
        $badgeClass = "badge-completed";
        break;
    case "cancelled":
        $badgeClass = "badge-cancelled";
        break;
    default:
        $badgeClass = "badge-secondary";
}
?>
<span class="badge badge-status <?= htmlspecialchars($badgeClass) ?>">
    <?= ucfirst(htmlspecialchars($status)) ?>
</span>
</div>

<small class="text-muted">
Ordered on: <?= date("d M Y, h:i A", strtotime($items[0]['created_at'])) ?>
</small>

<table class="table table-bordered mt-3">
<thead>
<tr>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>

<?php
$total = 0;
foreach ($items as $item):
$subtotal = $item['price'] * $item['quantity'];
$total += $subtotal;
?>
<tr>
<td><?= htmlspecialchars($item['product_name']) ?></td>
<td><?= htmlspecialchars($item['quantity']) ?></td>
<td>₹<?= number_format($item['price'],2) ?></td>
<td>₹<?= number_format($subtotal,2) ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<div class="text-end fw-bold">Total: ₹<?= number_format($total,2) ?></div>

</div>
<?php endforeach; ?>
<?php endif; ?>


<!-- CUSTOM ORDERS -->
<h4 class="section-title">🎨 Custom Orders</h4>

<?php if ($customOrders->num_rows === 0): ?>
<div class="alert alert-info">You have not placed any custom orders yet.</div>
<?php else: ?>

<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Product Type</th>
<th>Color</th>
<th>Size</th>
<th>Estimated Price</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>
<tbody>

<?php while($row = $customOrders->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['id']) ?></td>
<td><?= htmlspecialchars($row['product_type']) ?></td>
<td><?= htmlspecialchars($row['color']) ?></td>
<td><?= htmlspecialchars($row['size']) ?></td>
<td><?= $row['estimated_price'] ? "₹".number_format($row['estimated_price'],2) : "-" ?></td>

<?php
$cstatus = strtolower($row['status']);
switch ($cstatus) {
    case "pending":
        $cBadge = "badge-pending";
        break;
    case "reviewing":
        $cBadge = "badge-processing";
        break;
    case "priced":
        $cBadge = "badge-processing";
        break;
    case "accepted":
        $cBadge = "badge-completed";
        break;
    case "rejected":
        $cBadge = "badge-cancelled";
        break;
    default:
        $cBadge = "badge-secondary";
}
?>

<td>
<span class="badge badge-status <?= htmlspecialchars($cBadge) ?>">
    <?= ucfirst(htmlspecialchars($cstatus)) ?>
</span>
</td>
<td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
<?php endif; ?>

</div>
</body>
</html>
