<?php
session_start();
require_once "config.php";
require_once "products.php"; // load products from array

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =======================
   VALIDATE INPUT
======================== */
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0 || $quantity > 10) {
    header("Location: index.php?error=invalid");
    exit;
}

/* =======================
   FETCH PRODUCT FROM ARRAY
======================== */
$product = null;
foreach ($products as $p) {
    if ($p['id'] == $product_id) {
        $product = $p;
        break;
    }
}

if (!$product) {
    header("Location: index.php?error=notfound");
    exit;
}

if (!$product['inStock']) {
    header("Location: index.php?error=outofstock");
    exit;
}

$product_name = $product['name'];
$price        = (float)$product['price'];
$total_amount = $price * $quantity;

/* =======================
   FETCH USER DETAILS
======================== */
$userStmt = $conn->prepare("SELECT full_name, email, phone, address, city, pincode FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$user) {
    header("Location: index.php?error=user_not_found");
    exit;
}

$full_name = $user['full_name'];
$email     = $user['email'];
$phone     = $user['phone'];
$address   = $user['address'];
$city      = $user['city'];
$pincode   = $user['pincode'];

$payment_method = "COD";

/* =======================
   INSERT ORDER
======================== */
$conn->begin_transaction();

try {
    // Insert into orders table
    $insert = $conn->prepare("
        INSERT INTO orders
        (user_id, total_amount, payment_method, full_name, phone, email, address, city, pincode, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");

    $insert->bind_param(
        "idsssssss",
        $user_id,
        $total_amount,
        $payment_method,
        $full_name,
        $phone,
        $email,
        $address,
        $city,
        $pincode
    );

    $insert->execute();
    $order_id = $conn->insert_id;
    $insert->close();

    // Insert into order_items table
    $item_insert = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    $item_insert->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    $item_insert->execute();
    $item_insert->close();

    $conn->commit();

    // Redirect to success page
    header("Location: order_success.php?order_id=" . $order_id);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("Order Error: " . $e->getMessage());
    header("Location: index.php?error=failed");
    exit;
}
?>