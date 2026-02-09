<?php
// Get all items in user's cart with product details
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to view cart']);
    exit;
}

$user_id = $_SESSION['user_id'];

// join cart with products to get full details
$stmt = $conn->prepare("
    SELECT 
        c.id as cart_id,
        c.quantity,
        p.id as product_id,
        p.name,
        p.price,
        p.image_path,
        p.category,
        (p.price * c.quantity) as subtotal
    FROM cart c
    INNER JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['subtotal'];
}

echo json_encode([
    'success' => true,
    'cart_items' => $cart_items,
    'total' => number_format($total, 2, '.', ''),
    'count' => count($cart_items)
]);

$stmt->close();
$conn->close();
?>
