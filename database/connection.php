<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
// Include your database configuration
require 'config.php'; // Ensure this file contains the database credentials as shown earlier

// Function to test a connection
function testConnection($host, $username, $password, $dbname) {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo "Connection to database '$dbname' failed: " . $conn->connect_error . "\n";
        return false;
    } else {
        echo "Connected successfully to database '$dbname'.\n";
        $conn->close();
        return true;
    }
}

// Test the connections
echo "Testing connections...\n";
$success1 = testConnection($host, $username, $password, $dbname1);
$success2 = testConnection($host, $username, $password, $dbname2);

if ($success1 && $success2) {
    echo "All database connections were successful.\n";
} else {
    echo "Some database connections failed. Check your configuration.\n";
}
?>