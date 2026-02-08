<?php
session_start();
require_once "config.php";
require_once "products.php"; // load products array

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   BUILD CART FROM POST
========================= */
$cart = [];

if (isset($_POST['cart']) && is_array($_POST['cart'])) {
    foreach ($_POST['cart'] as $pid => $item) {
        $pid = (int)$pid;
        $qty = (int)$item['quantity'];

        if ($qty <= 0) continue;

        // Find product from products.php
        foreach ($products as $p) {
            if ($p['id'] == $pid && $p['inStock']) {
                $cart[] = [
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'image' => $p['images'][0],
                    'quantity' => $qty
                ];
                break;
            }
        }
    }
}

$_SESSION['cart'] = $cart;

/* =========================
   FETCH USER DETAILS
========================= */
$stmt = $conn->prepare("SELECT full_name, email, phone, address, city, pincode FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = '';
$field_errors = [];

/* =========================
   HANDLE ORDER SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {

    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    $promoCode = trim($_POST['promo_code'] ?? '');

    if (empty($fullName)) $field_errors['full_name'] = "Name is required";
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) $field_errors['phone'] = "Invalid phone";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $field_errors['email'] = "Invalid email";
    if (strlen($address) < 10) $field_errors['address'] = "Address too short";
    if (empty($city)) $field_errors['city'] = "City required";
    if (!preg_match('/^\d{6}$/', $pincode)) $field_errors['pincode'] = "Invalid pincode";
    if (empty($payment_method)) $field_errors['payment_method'] = "Select payment method";

    if ($promoCode !== '' && $promoCode !== "WELCOME10") {
        $field_errors['promo_code'] = "Invalid promo code";
    }

    if (empty($cart)) {
        $error = "Your cart is empty.";
    }
    elseif (!empty($field_errors)) {
        $error = "Please fix errors.";
    }
    else {

        /* ===== CALCULATE TOTAL ===== */
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = ($promoCode === "WELCOME10") ? round($subtotal * 0.10, 2) : 0;
        $shipping = ($subtotal >= 2000) ? 0 : 50;
        $tax = round($subtotal * 0.18, 2);
        $totalAmount = $subtotal + $shipping + $tax - $discount;

        $conn->begin_transaction();

        try {
            // Insert order
            $stmt = $conn->prepare("
                INSERT INTO orders
                (user_id, total_amount, payment_method, full_name, phone, email, address, city, pincode, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
            ");

            $stmt->bind_param(
                "idsssssss",
                $user_id,
                $totalAmount,
                $payment_method,
                $fullName,
                $phone,
                $email,
                $address,
                $city,
                $pincode
            );

            $stmt->execute();
            $orderId = $conn->insert_id;

            // Insert order items
            $itemStmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($cart as $item) {
                $itemStmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }

            $conn->commit();
            unset($_SESSION['cart']);

           // Clear cart session
            unset($_SESSION['cart']);

            // Redirect using POST-Redirect-GET pattern
             header("Location: order_success.php?order_id=" . $orderId);
               exit;

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Order failed. Try again.";
        }
    }
}

/* =========================
   PRICE FOR DISPLAY
========================= */
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = ($subtotal >= 2000) ? 0 : 50;
$tax = round($subtotal * 0.18, 2);
$totalAmount = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Crochet Haven</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #ff9eb5;
            --secondary-color: #a8e6cf;
            --accent-color: #ffd3b6;
            --dark-color: #6d6875;
            --light-color: #fff9f4;
            --text-color: #5a5a5a;
            --border-radius: 12px;
            --box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .checkout-section { background: white; border-radius: var(--border-radius); padding: 2rem; box-shadow: var(--box-shadow); margin-bottom: 2rem; }
        .cart-item { display: flex; align-items: center; padding: 1rem 0; border-bottom: 1px solid #eee; }
        .cart-item img { width: 80px; height: 80px; border-radius: var(--border-radius); object-fit: contain; }
        .summary-breakdown { font-size: 0.95rem; display:flex; justify-content:space-between; margin-bottom: 6px; }
        .summary-total { border-top: 1px solid #eee; padding-top: 10px; font-weight: bold; font-size: 1.2rem; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: #ff7b9d; }
        .is-invalid { border-color: red; }
        .invalid-feedback { display: block; }
        .promo-feedback { margin-top: 5px; }

        .payment-option:hover {
    background-color: #fff1f4;
    cursor: pointer;
    transition: 0.3s;
}
    </style>
</head>

<body>

        <?php include 'navbar.php'; ?>

<div class="container my-5">

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- LEFT SIDE -->
        <div class="col-lg-8">
            <h2><i class="fas fa-shopping-cart me-2"></i> Secure Checkout</h2>
            <p class="text-muted mb-4">Review your order & complete purchase securely.</p>

            <form method="POST" action="checkout.php" id="checkoutForm">

                <!-- ================= SHIPPING INFO ================= -->
                <div class="checkout-section">
                    <h4><i class="fas fa-user me-2"></i> Shipping Information</h4>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name"
                                   value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                   value="<?= htmlspecialchars($user['phone']) ?>" required maxlength="10">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city"
                                   value="<?= htmlspecialchars($user['city']) ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="pincode"
                                   value="<?= htmlspecialchars($user['pincode']) ?>" required maxlength="6">
                        </div>

                        <!-- Promo Code -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Promo Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promoCode" name="promo_code" placeholder="WELCOME10">
                                <button class="btn btn-outline-secondary" type="button" id="applyPromo">Apply</button>
                            </div>
                            <div id="promoFeedback" class="promo-feedback"></div>
                        </div>

                    </div>
                </div>

                <!-- ================= PAYMENT METHOD ================= -->
<div class="checkout-section mt-3">
    <h4><i class="fas fa-credit-card me-2"></i> Payment Method</h4>
    <p class="text-muted">Choose a secure payment option</p>

    <div class="payment-option border rounded p-3 mb-2">
        <div class="form-check d-flex align-items-center">
            <input class="form-check-input me-2" type="radio" name="payment_method" value="COD" id="cod" checked required>
            <label class="form-check-label w-100" for="cod">
                <i class="fas fa-money-bill-wave text-success me-2"></i>
                <strong>Cash on Delivery</strong>
                <span class="text-muted d-block">Pay when your order arrives</span>
            </label>
        </div>
    </div>

    <div class="payment-option border rounded p-3 mb-2">
        <div class="form-check d-flex align-items-center">
            <input class="form-check-input me-2" type="radio" name="payment_method" value="UPI" id="upi">
            <label class="form-check-label w-100" for="upi">
                <i class="fab fa-google-pay text-primary me-2"></i>
                <strong>UPI / Google Pay / PhonePe</strong>
                <span class="text-muted d-block">Fast & secure digital payment</span>
            </label>
        </div>
    </div>

    <div class="payment-option border rounded p-3">
        <div class="form-check d-flex align-items-center">
            <input class="form-check-input me-2" type="radio" name="payment_method" value="Card" id="card">
            <label class="form-check-label w-100" for="card">
                <i class="fas fa-credit-card text-warning me-2"></i>
                <strong>Credit / Debit Card</strong>
                <span class="text-muted d-block">Visa, MasterCard, RuPay supported</span>
            </label>
        </div>
    </div>
</div> 


                   <!-- ================= DELIVERY INFORMATION ================= -->
<div class="checkout-section mt-3">
    <h4><i class="fas fa-truck me-2"></i> Delivery Information</h4>

    <ul class="list-unstyled mt-3">
        <li class="mb-2">
            <i class="fas fa-clock text-primary me-2"></i>
            <strong>Estimated Delivery:</strong> 
            <span>3 – 5 Business Days</span>
        </li>

        <li class="mb-2">
            <i class="fas fa-map-marker-alt text-danger me-2"></i>
            <strong>Shipping Address:</strong> 
            <span><?= htmlspecialchars($user['city']) ?>, <?= htmlspecialchars($user['pincode']) ?></span>
        </li>

        <li class="mb-2">
            <i class="fas fa-box text-success me-2"></i>
            <strong>Courier Partner:</strong> 
            <span>Yarnora Express Delivery</span>
        </li>

        <li class="mb-2">
            <i class="fas fa-shield-alt text-info me-2"></i>
            <strong>Secure Packaging:</strong> 
            <span>Your crochet items are packed safely</span>
        </li>
    </ul>

    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>
        Orders above ₹2000 qualify for <strong>Free Shipping</strong>.
    </div>
</div>

                <!-- ================= HIDDEN CART DATA ================= -->
                <?php foreach ($cart as $item): ?>
                    <?php foreach ($item as $k => $v): ?>
                        <input type="hidden" name="cart[<?= $item['id'] ?>][<?= $k ?>]"
                               value="<?= htmlspecialchars($v) ?>">
                    <?php endforeach; ?>
                <?php endforeach; ?>

            </form>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-4">
            <div class="checkout-section sticky-top">

                <h4><i class="fas fa-receipt me-2"></i> Order Summary</h4>

                <?php if (empty($cart)): ?>
                    <div class="alert alert-warning">Your cart is empty</div>
                <?php else: ?>

                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="">
                            <div class="ms-3">
                                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                ₹<?= number_format($item['price'], 2) ?> × <?= htmlspecialchars($item['quantity']) ?><br>
                                <span class="text-primary fw-bold">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="summary-breakdown" id="subtotalRow">
                        <span>Subtotal:</span>
                        <span>₹<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <div class="summary-breakdown" id="discountRow" style="display: none;">
                        <span>Discount:</span>
                        <span id="discountAmount">₹0.00</span>
                    </div>

                    <div class="summary-breakdown">
                        <span>Shipping:</span>
                        <span id="shippingAmount"><?= $shipping == 0 ? "Free" : "₹{$shipping}" ?></span>
                    </div>

                    <div class="summary-breakdown">
                        <span>GST (18%):</span>
                        <span id="taxAmount">₹<?= number_format($tax, 2) ?></span>
                    </div>

                    <div class="summary-total d-flex justify-content-between">
                        <span>Total:</span>
                        <span class="text-primary" id="totalAmount">₹<?= number_format($totalAmount, 2) ?></span>
                    </div>

                    <button type="submit" form="checkoutForm" id="placeOrderBtn"
                            class="btn btn-primary w-100 mt-3">
                        Complete Order (₹<?= number_format($totalAmount, 2) ?>)
                    </button>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("checkoutForm");
    const promoInput = document.getElementById("promoCode");
    const promoBtn = document.getElementById("applyPromo");
    const promoFeedback = document.getElementById("promoFeedback");
    const placeOrderBtn = document.getElementById("placeOrderBtn");
    const paymentRadios = document.querySelectorAll("input[name='payment_method']");
    const totalAmountSpan = document.getElementById("totalAmount");
    const discountRow = document.getElementById("discountRow");
    const discountAmount = document.getElementById("discountAmount");
    const shippingAmount = document.getElementById("shippingAmount");
    const taxAmount = document.getElementById("taxAmount");

    if (!form) return;

    let subtotal = <?= $subtotal ?>;
    let shipping = <?= $shipping ?>;
    let tax = subtotal * 0.18;
    let discount = 0;
    let promoApplied = false;

    /* ------------------------------
       Update Total Function
    --------------------------------*/
    function updateTotal() {
        tax = subtotal * 0.18;
        const total = subtotal + shipping + tax - discount;
        if (totalAmountSpan) totalAmountSpan.innerText = '₹' + total.toFixed(2);
        if (placeOrderBtn) placeOrderBtn.innerText = 'Complete Order (₹' + total.toFixed(2) + ')';
        if (taxAmount) taxAmount.innerText = '₹' + tax.toFixed(2);
        if (discountRow) discountRow.style.display = discount > 0 ? 'flex' : 'none';
        if (discountAmount) discountAmount.innerText = '₹' + discount.toFixed(2);
        if (shippingAmount) shippingAmount.innerText = shipping === 0 ? "Free" : '₹' + shipping;
    }

    updateTotal(); // Initial

    /* ------------------------------
       Form Validation Function
    --------------------------------*/
    function validateForm() {
        let valid = true;

        const fields = form.querySelectorAll("input[required], textarea[required]");

        fields.forEach(field => {
            field.classList.remove("is-invalid");

            if (!field.value.trim()) {
                field.classList.add("is-invalid");
                valid = false;
            }

            // Email validation
            if (field.name === "email") {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(field.value.trim())) {
                    field.classList.add("is-invalid");
                    valid = false;
                }
            }

            // Phone validation
            if (field.name === "phone") {
                const phoneRe = /^[6-9]\d{9}$/;
                if (!phoneRe.test(field.value.trim())) {
                    field.classList.add("is-invalid");
                    valid = false;
                }
            }

            // Pincode validation
            if (field.name === "pincode") {
                const pinRe = /^\d{6}$/;
                if (!pinRe.test(field.value.trim())) {
                    field.classList.add("is-invalid");
                    valid = false;
                }
            }

            // Address length
            if (field.name === "address" && field.value.trim().length < 10) {
                field.classList.add("is-invalid");
                valid = false;
            }
        });

        // Payment method validation
        let paymentSelected = false;
        paymentRadios.forEach(r => {
            if (r.checked) paymentSelected = true;
        });

        if (!paymentSelected) {
            valid = false;
            alert("Please select a payment method.");
        }

        // Promo must be valid
        if (!promoApplied && promoInput.value.trim() !== "") valid = false;

        if (placeOrderBtn) placeOrderBtn.disabled = !valid;

        return valid;
    }

    /* -----------------------------------
       Apply Promo Code
    ----------------------------------- */
    if (promoBtn) {
        promoBtn.addEventListener("click", () => {

            const code = promoInput.value.trim().toUpperCase();
            promoFeedback.innerHTML = "";
            promoInput.classList.remove("is-invalid", "is-valid");

            if (code === "") {
                promoApplied = true;
                discount = 0;
                promoFeedback.innerHTML = "";
            }
            else if (code === "WELCOME10") {
                promoApplied = true;
                discount = subtotal * 0.10;
                promoInput.classList.add("is-valid");
                promoFeedback.innerHTML = `
                    <div class="alert alert-success p-1 m-0">
                        ✔️ Promo Applied: 10% discount!
                    </div>
                `;
            }
            else {
                promoApplied = false;
                discount = 0;
                promoInput.classList.add("is-invalid");
                promoFeedback.innerHTML = `
                    <div class="alert alert-danger p-1 m-0">
                        ❌ Invalid Promo Code
                    </div>
                `;
            }

            updateTotal();
            validateForm();
        });
    }

    /* -----------------------------------
       Real-time validation
    ----------------------------------- */
    form.addEventListener("input", validateForm);
    form.addEventListener("change", validateForm);

    paymentRadios.forEach(radio => {
        radio.addEventListener("change", validateForm);
    });

    /* -----------------------------------
       Final submit check
    ----------------------------------- */
    form.addEventListener("submit", function (e) {
        if (!validateForm()) {
            e.preventDefault();
            alert("Please correct the errors before submitting.");
            return false;
        }
    });

    validateForm();
});
</script>
</body>
</html>