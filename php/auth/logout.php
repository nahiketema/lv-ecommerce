<?php
// Logout - destroys session
session_start();

$_SESSION = array(); // clear everything

// kill the cookie too
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);
?>
