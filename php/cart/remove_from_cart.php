<?php
// Remove item from cart
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? 0;

if (empty($cart_id) || !is_numeric($cart_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

// make sure user owns this cart item
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Item removed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in your cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Could not remove item']);
}

$stmt->close();
$conn->close();
?>
