<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'vinyl_records_shop');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Session configuration
session_start();

// Define user roles
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');
define('ROLE_GUEST', 'guest');

// Set default timezone
date_default_timezone_set('Europe/Athens');
?>
