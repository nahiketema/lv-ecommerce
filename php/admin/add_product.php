<?php
// Add new product (admin only)
// handles image upload too
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$price = $_POST['price'] ?? 0;
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? '';

if (empty($name) || empty($price) || empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
    exit;
}

// handle image upload
$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $upload_dir = '../../uploads/';
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // only allow these types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG and GIF images allowed']);
        exit;
    }
    
    // unique filename to avoid overwrites
    $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $image_path = 'uploads/' . $new_filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Image upload failed']);
        exit;
    }
}

// insert into db
$stmt = $conn->prepare("INSERT INTO products (name, price, description, category, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdsss", $name, $price, $description, $category, $image_path);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Product added!',
        'product_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product']);
}

$stmt->close();
$conn->close();
?>
