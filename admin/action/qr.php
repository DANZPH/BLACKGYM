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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
    <div id="qrcode"></div> <!-- Container where QR code will be generated -->
    
    <script>
        // Fetch the latest receipt number from the backend
        fetch('getReceiptNumber.php')  // Make sure the PHP script is returning JSON data correctly
            .then(response => response.json()) // Expecting JSON data from the PHP backend
            .then(data => {
                console.log(data); // Check if you get the receipt number here
                const receiptNumber = data.receiptNumber;
                
                // Generate the QR Code using the QRCode.js library
                QRCode.toCanvas(document.getElementById('qrcode'), receiptNumber, function (error) {
                    if (error) console.error(error);
                    console.log('QR code successfully generated!');
                });
            })
            .catch(error => {
                console.error("Error fetching receipt number:", error);
            });
    </script>
</body>
</html>