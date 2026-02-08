<?php
session_start();
require_once "config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success"=>false,"message"=>"Login required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;
$rating = $_POST['rating'] ?? 0;
$review_text = trim($_POST['review_text'] ?? '');

if ($product_id == 0 || $rating == 0 || $review_text == "") {
    echo json_encode(["success"=>false,"message"=>"All fields required"]);
    exit;
}

$uploadDir = "uploads/reviews/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$photoPaths = [];

// Handle multiple images
if (!empty($_FILES['photos']['name'][0])) {
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
        $fileName = time() . "_" . basename($_FILES['photos']['name'][$key]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $photoPaths[] = $targetPath;
        }
    }
}

$photosJson = json_encode($photoPaths);

// Insert or Update review (because UNIQUE(product_id,user_id))
$stmt = $conn->prepare("
INSERT INTO reviews (product_id, user_id, rating, review_text, photos)
VALUES (?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
rating = VALUES(rating),
review_text = VALUES(review_text),
photos = VALUES(photos),
created_at = CURRENT_TIMESTAMP
");

$stmt->bind_param("iiiss", $product_id, $user_id, $rating, $review_text, $photosJson);

if ($stmt->execute()) {
    echo json_encode(["success"=>true,"message"=>"Review saved"]);
} else {
    echo json_encode(["success"=>false,"message"=>"Database error"]);
}

$stmt->close();
$conn->close();
?>