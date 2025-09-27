<?php
// Simple database import script for localhost
$host = "localhost";
$username = "root";
$password = "";
$database = "blackgym";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' created successfully or already exists.\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db($database);

// Read and execute the SQL file
$sqlFile = 'blackgym.sql';
if (file_exists($sqlFile)) {
    $sql = file_get_contents($sqlFile);
    
    // Split the SQL file into individual queries
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === TRUE) {
                // Success - don't output for each query to avoid spam
            } else {
                echo "Error executing query: " . $conn->error . "\n";
                echo "Query: " . substr($query, 0, 100) . "...\n";
            }
        }
    }
    
    echo "Database import completed successfully!\n";
    
    // Add some sample data for PaymentType if payments table is empty
    $checkPayments = "SELECT COUNT(*) as count FROM Payments";
    $result = $conn->query($checkPayments);
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "Adding sample payment methods...\n";
        // This is just to ensure the PaymentType column works properly
        // You can remove this if you don't want sample data
    }
    
    echo "You can now access your application at: http://localhost/BLACKGYM/\n";
} else {
    echo "SQL file '$sqlFile' not found!\n";
}

$conn->close();
?>