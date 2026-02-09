<?php
// Get all orders (admin view)
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// only admins can see all orders
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

// get orders with customer info
// TODO: add pagination for when there are lots of orders
$result = $conn->query("
    SELECT 
        o.id,
        o.total_amount,
        o.shipping_address,
        o.order_date,
        o.status,
        u.full_name as customer_name,
        u.email as customer_email
    FROM orders o
    INNER JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    'success' => true,
    'orders' => $orders,
    'count' => count($orders)
]);

$conn->close();
?>
