<?php
// Get products - returns all products or filtered by category
require_once '../db.php';

header('Content-Type: application/json');

$category = $_GET['category'] ?? '';

if (!empty($category)) {
    // filter by category
    $stmt = $conn->prepare("SELECT id, name, price, description, category, image_path FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // get all
    $result = $conn->query("SELECT id, name, price, description, category, image_path FROM products ORDER BY created_at DESC");
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    'success' => true,
    'products' => $products,
    'count' => count($products)
]);

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
