<?php
/**
 * Get Single Product Handler
 * Fetches detailed information for a specific product by ID
 */

// Include database connection
require_once '../db.php';

// Set header for JSON response
header('Content-Type: application/json');

// Get product ID from query parameter
$product_id = $_GET['id'] ?? 0;

// Validate product ID
if (empty($product_id) || !is_numeric($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Query database for product
$stmt = $conn->prepare("SELECT id, name, price, description, category, image_path FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if product exists
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Get product data
$product = $result->fetch_assoc();

// Return product data
echo json_encode([
    'success' => true,
    'product' => $product
]);

$stmt->close();
$conn->close();
?>
