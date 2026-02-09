<?php
// Place order - converts cart to an order
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to checkout']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$shipping_address = trim($_POST['shipping_address'] ?? '');

if (empty($shipping_address)) {
    echo json_encode(['success' => false, 'message' => 'Shipping address is required']);
    exit;
}

// calculate cart total
$cart_query = $conn->prepare("
    SELECT SUM(p.price * c.quantity) as total
    FROM cart c
    INNER JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$result = $cart_query->get_result();
$cart_data = $result->fetch_assoc();
$total_amount = $cart_data['total'] ?? 0;

if ($total_amount == 0) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
    exit;
}

// create order record
// TODO: maybe add order_items table to store individual items?
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'Placed')");
$stmt->bind_param("ids", $user_id, $total_amount, $shipping_address);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    
    // clear cart
    $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();
    $clear_cart->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'total' => number_format($total_amount, 2, '.', '')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not place order. Please try again.']);
}

$cart_query->close();
$stmt->close();
$conn->close();
?>
