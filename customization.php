<?php
session_start();
require_once "config.php";

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user = null;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT full_name, email, phone, address, city, pincode
        FROM users WHERE id = ?
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

/* -----------------------------
   FORM SUBMISSION LOGIC
--------------------------------*/
if (isset($_POST['submit'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $name        = htmlspecialchars(trim($_POST['name']));
    $email       = htmlspecialchars(trim($_POST['email']));
    $phone       = htmlspecialchars(trim($_POST['phone']));
    $address     = htmlspecialchars(trim($_POST['address']));
    $city        = htmlspecialchars(trim($_POST['city']));
    $pincode     = htmlspecialchars(trim($_POST['pincode']));

    $product_type = htmlspecialchars(trim($_POST['product_type']));
    $color        = htmlspecialchars(trim($_POST['color'] ?? ""));
    $size         = htmlspecialchars(trim($_POST['size'] ?? ""));
    $description  = htmlspecialchars(trim($_POST['details']));

    if (empty($name) || empty($email) || empty($phone) || empty($product_type) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {

      /* ===== IMAGE UPLOAD ===== */
$imagePath = null;

if (!empty($_FILES['reference']['name'])) {

    $uploadDir = "uploads/custom_orders/";

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = $_FILES['reference']['type'];

    if (!in_array($fileType, $allowedTypes)) {
        $error = "Invalid file type. Upload JPG, PNG or GIF.";
    } 
    elseif ($_FILES['reference']['size'] > 5 * 1024 * 1024) {
        $error = "File too large. Max 5MB.";
    } 
    else {
        $fileName = time() . "_" . basename($_FILES['reference']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['reference']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile; // save full path
        } else {
            $error = "Image upload failed.";
        }
    }
}
    

if (!isset($error)) {

    $q = "
        INSERT INTO custom_orders
        (user_id, name, email, phone, product_type, color, size, description, reference_image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($q);
    $stmt->bind_param(
        "issssssss",
        $user_id,
        $name,
        $email,
        $phone,
        $product_type,
        $color,
        $size,
        $description,
        $imagePath
    );

    if ($stmt->execute()) {
        header("Location: customization.php?success=1");
        exit;
    } else {
        $error = "Error saving request.";
    }
}
}

}

$successMessage = "";
if (isset($_GET['success'])) {
    $successMessage = "Customization request submitted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customization | Crochet Haven</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-color: #ff9eb5;
    --dark-color: #6d6875;
    --light-color: #fff9f4;
    --radius: 12px;
}
body { font-family: 'Poppins', sans-serif; }

/* HERO */
.customization-hero {
    padding: 3rem 0;
    background: linear-gradient(135deg, #ff9eb5, #ff7b9d);
    text-align: center;
    color: white;
}

/* FORM */
.form-section {
    background: var(--light-color);
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.form-section h2 { font-weight: 600; }

.file-upload {
    border: 2px dashed #ccc;
    padding: 1.2rem;
    border-radius: var(--radius);
    text-align: center;
}

/* BUTTON */
.btn-submit {
    background: var(--primary-color);
    border: none;
    font-weight: 600;
    padding: 0.8rem;
}
.btn-submit:hover {
    background: #ff7b9d;
}

/* NAVBAR simplified */
.navbar-brand { color: var(--primary-color) !important; font-weight: 700; }
</style>
</head>
<body>
<!-- NAVBAR (clean version) -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">Crochet Haven</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="customization.php">Customization</a></li>
                <li class="nav-item"><a class="nav-link" href="sustainability.php">Sustainability</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>

            <div class="d-flex">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-user fs-4"></i>
                    </a>
                <?php else: ?>
                    <a href="profile.php" class="me-3">
                        <i class="fas fa-user fs-4"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="customization-hero">
    <h1>Customize Your Crochet</h1>
    <p>Tell us your idea — we will craft it beautifully.</p>
</section>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="form-section">
                <h2 class="mb-4 text-center">Custom Order Form</h2>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Your Full Name *</label>
                        <input type="text" class="form-control"
                               name="name"
                               required
                               value="<?= htmlspecialchars($_POST['name'] ?? $user['full_name'] ?? "") ?>">
                    </div>

                    <div class="mb-3">
                        <label>Email *</label>
                        <input type="email" class="form-control"
                               name="email"
                               required
                               value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? "") ?>">
                    </div>

                    <div class="mb-3">
                        <label>Phone *</label>
                        <input type="text" class="form-control"
                               name="phone" required
                               value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? "") ?>">
                    </div>

                    <div class="mb-3">
                        <label>Address *</label>
                        <textarea class="form-control" name="address" rows="2" required><?= htmlspecialchars($_POST['address'] ?? $user['address'] ?? "") ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>City *</label>
                            <input type="text" class="form-control" name="city" required
                                   value="<?= htmlspecialchars($_POST['city'] ?? $user['city'] ?? "") ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pincode *</label>
                            <input type="text" class="form-control" name="pincode" required
                                   value="<?= htmlspecialchars($_POST['pincode'] ?? $user['pincode'] ?? "") ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label>Product Type *</label>
                        <select class="form-select" name="product_type" required>
                            <option value="">Select…</option>
                            <option value="Bag">Bag</option>
                            <option value="Keychain">Keychain</option>
                            <option value="Scarf">Scarf</option>
                            <option value="Decor">Home Decor</option>
                            <option value="Jewelry">Jewelry</option>
                            <option value="Clothing">Clothing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Preferred Colors</label>
                        <input type="text" class="form-control" name="color">
                    </div>

                    <div class="mb-3">
                        <label>Size</label>
                        <input type="text" class="form-control" name="size">
                    </div>

                    <div class="mb-3">
                        <label>Description *</label>
                        <textarea class="form-control" name="details" required rows="5"></textarea>
                    </div>
                       <div class="mb-3">
                        <label>Reference Image (optional)</label>
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                            <p>Upload image</p>
                            <input type="file" class="form-control" name="reference" accept="image/*">
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-submit w-100">
                        Submit Request
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>