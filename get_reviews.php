<?php
require_once "config.php";

header("Content-Type: application/json");

$product_id = $_GET['product_id'] ?? 0;

if ($product_id == 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
SELECT r.rating, r.review_text, r.photos, r.created_at, u.full_name
FROM reviews r
JOIN users u ON r.user_id = u.id
WHERE r.product_id = ?
ORDER BY r.created_at DESC
");

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];

while ($row = $result->fetch_assoc()) {
    $row['photos'] = json_decode($row['photos'], true); // convert JSON to array
    $reviews[] = $row;
}

echo json_encode($reviews);

$stmt->close();
$conn->close();
?>