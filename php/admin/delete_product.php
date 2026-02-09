<?php
// Delete product (admin only)
// also deletes the image file
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$product_id = $_POST['product_id'] ?? 0;

if (empty($product_id) || !is_numeric($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// get image path first so we can delete the file
$get_image = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
$get_image->bind_param("i", $product_id);
$get_image->execute();
$result = $get_image->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $image_path = $product['image_path'];
    
    // delete from db
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        // try to delete image file too
        if (!empty($image_path) && file_exists('../../' . $image_path)) {
            unlink('../../' . $image_path);
        }
        echo json_encode(['success' => true, 'message' => 'Product deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not delete product']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}

$get_image->close();
$conn->close();
?>
