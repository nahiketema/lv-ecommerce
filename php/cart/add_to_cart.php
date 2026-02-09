<?php
// Add item to cart
// If product already in cart, just update the quantity instead
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (empty($product_id) || !is_numeric($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

// verify product exists
$check_product = $conn->prepare("SELECT id FROM products WHERE id = ?");
$check_product->bind_param("i", $product_id);
$check_product->execute();
if ($check_product->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}
$check_product->close();

// check if already in cart
$check_cart = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$check_cart->bind_param("ii", $user_id, $product_id);
$check_cart->execute();
$result = $check_cart->get_result();

if ($result->num_rows > 0) {
    // already exists - just add to quantity
    $cart_item = $result->fetch_assoc();
    $new_quantity = $cart_item['quantity'] + $quantity;
    
    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update_stmt->bind_param("ii", $new_quantity, $cart_item['id']);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not update cart']);
    }
    $update_stmt->close();
} else {
    // new item
    $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Added to cart!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item']);
    }
    $insert_stmt->close();
}

$check_cart->close();
$conn->close();
?>
