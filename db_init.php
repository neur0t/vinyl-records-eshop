<?php
// Database Initialization Script
// Run this script once to set up the database

$servername = "localhost";
$username = "root";
$password = "12345";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the SQL file
$sql = file_get_contents('database.sql');

// Execute multiple statements
if ($conn->multi_query($sql)) {
    echo "Database and tables created successfully!<br>";
    echo "Sample data inserted!<br>";
    
    // Get all results from multi_query
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "✓ Database setup complete!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
