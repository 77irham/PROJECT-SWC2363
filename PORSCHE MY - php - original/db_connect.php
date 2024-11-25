<?php
// Database connection settings
$servername = "localhost"; // Database host
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "porsche_my"; // Database name

// Create connection with exception handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check if the connection is successful
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Display error message if connection fails
    die("Database connection error: " . $e->getMessage());
}
?>
