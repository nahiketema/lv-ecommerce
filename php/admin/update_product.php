<?php
// Update product details (admin only)
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
$name = trim($_POST['name'] ?? '');
$price = $_POST['price'] ?? 0;
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? '';

if (empty($product_id) || !is_numeric($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if (empty($name) || empty($price) || empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Name, price, and category are required']);
    exit;
}

// check for new image upload
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $upload_dir = '../../uploads/';
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }
    
    $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $image_path = 'uploads/' . $new_filename;
    }
}

// update db
if ($image_path) {
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, category = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sdsssi", $name, $price, $description, $category, $image_path, $product_id);
} else {
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $name, $price, $description, $category, $product_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>
