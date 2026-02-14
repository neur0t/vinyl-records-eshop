<?php
// Database Initialization Script
// Run this script once to set up the database

$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default: empty root password

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the SQL file
$sql = file_get_contents('database.sql');

// Split into individual statements and execute each one to be tolerant
$statements = array_filter(array_map('trim', explode(';', $sql)));
$errors = [];
foreach ($statements as $stmt) {
    if ($stmt === '') continue;
    // Skip comments
    if (strpos($stmt, '--') === 0) continue;

    try {
        $conn->query($stmt);
    } catch (mysqli_sql_exception $e) {
        $errno = $e->getCode();
        // Ignore harmless 'table exists' (1050), 'duplicate key name' (1061) and 'duplicate entry' (1062)
        if (in_array($errno, [1050, 1061, 1062])) {
            continue;
        }
        $errors[] = "Error ({$errno}): " . $e->getMessage() . " -- SQL: " . substr($stmt, 0, 200);
    }
}

if (empty($errors)) {
    echo "Database and tables created/verified successfully!<br>";
    echo "Sample data inserted (if not present).<br>";
    echo "✓ Database setup complete!";
} else {
    echo "Setup completed with errors:<br>";
    foreach ($errors as $e) {
        echo htmlspecialchars($e) . '<br>';
    }
}

$conn->close();
?>
