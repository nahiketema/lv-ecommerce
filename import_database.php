<?php
/**
 * Database Import Script
 * This script will create the database and import the SQL file
 * Navigate to: http://localhost/lv-ecommerce/import_database.php
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lv_ecommerce';

echo "<h1>Louis Vuitton E-Commerce - Database Import</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 40px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";

try {
    // Connect to MySQL server (without selecting database)
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p class='success'>✓ Connected to MySQL server successfully</p>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>✓ Database '$dbname' created or already exists</p>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($dbname);
    echo "<p class='success'>✓ Database '$dbname' selected</p>";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database/setup.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found at: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    echo "<p class='info'>→ Reading SQL file...</p>";
    
    // Execute multi-query
    if ($conn->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
    
    if ($conn->error) {
        throw new Exception("Error importing database: " . $conn->error);
    }
    
    echo "<p class='success'>✓ Database imported successfully!</p>";
    
    // Verify tables were created
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "<h3>Tables Created:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Count products
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    echo "<p class='success'>✓ Total products in database: " . $row['count'] . "</p>";
    
    // Count users
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<p class='success'>✓ Total users in database: " . $row['count'] . "</p>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✓ Setup Complete!</h2>";
    echo "<p>You can now access the website:</p>";
    echo "<ul>";
    echo "<li><a href='index.html' style='font-size: 18px; color: #D4AF37;'><strong>Visit Homepage</strong></a></li>";
    echo "<li><a href='shop.html' style='font-size: 18px; color: #D4AF37;'><strong>Shop Products</strong></a></li>";
    echo "<li><a href='login.html' style='font-size: 18px; color: #D4AF37;'><strong>Login</strong></a></li>";
    echo "</ul>";
    
    echo "<h3>Demo Accounts:</h3>";
    echo "<p><strong>Admin:</strong> admin@lv.com / admin123</p>";
    echo "<p><strong>Customer:</strong> customer@lv.com / customer123</p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL is running in XAMPP Control Panel</li>";
    echo "<li>MySQL credentials are correct (default: root with no password)</li>";
    echo "<li>SQL file exists at: database/setup.sql</li>";
    echo "</ul>";
}
?>
