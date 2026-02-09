<?php
/*
 * Database connection
 * Using MySQLi - connects to lv_ecommerce db
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lv_ecommerce');
define('DB_PORT', 3307);  // custom port to avoid conflicts

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// utf8mb4 for emoji support etc
$conn->set_charset("utf8mb4");
?>
