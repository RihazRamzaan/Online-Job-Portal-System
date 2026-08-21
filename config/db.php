<?php
// Database connection configuration
$host = 'localhost';
$db_name = 'job_portal_db';
$username = 'root'; // default XAMPP username
$password = '';     // default XAMPP password

// Enable MySQLi error reporting (throws exceptions on errors instead of warnings)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish a connection to the MySQL database
    $conn = new mysqli($host, $username, $password, $db_name);
    
    // Set the character set to utf8mb4 for full Unicode support
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // If connection fails, catch the exception and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>