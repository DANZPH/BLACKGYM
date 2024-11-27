<?php
include '../../database/connection.php';

// Ensure the database connection is valid
if (!$conn1) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch the latest receipt number
$query = "SELECT ReceiptNumber FROM Payments ORDER BY PaymentDate DESC LIMIT 1";
$result = $conn1->query($query);

if (!$result) {
    die("Query failed: " . $conn1->error);
}

// Fetch the result
$row = $result->fetch_assoc();
if (!$row) {
    die("No receipt found.");
}

$receiptNumber = $row['ReceiptNumber'];

// Return the receipt number as a JSON response
header('Content-Type: application/json');
echo json_encode(['receiptNumber' => $receiptNumber]);
?>