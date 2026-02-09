<?php
// Update cart quantity
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Bad request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (empty($cart_id) || !is_numeric($cart_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

// update only if user owns the item
$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("iii", $quantity, $cart_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or no change needed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>
