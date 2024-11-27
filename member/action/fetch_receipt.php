<?php
session_start();  // Start the session to store session variables
include '../../database/connection.php';  // Include the database connection

// Check if the user is logged in
if (isset($_SESSION['MemberID'])) {
    $memberID = $_SESSION['MemberID']; // Retrieve the MemberID from the session

    // Query to fetch the latest ReceiptNumber for the logged-in member
    $receipt_query = $conn1->prepare("SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1");
    $receipt_query->bind_param("i", $memberID);
    $receipt_query->execute();
    $receipt_result = $receipt_query->get_result();

    // Check if a receipt is found
    if ($receipt_result->num_rows > 0) {
        $receipt = $receipt_result->fetch_assoc();
        $_SESSION['latestReceiptNumber'] = $receipt['ReceiptNumber']; // Store the latest receipt number in session
    } else {
        $_SESSION['latestReceiptNumber'] = null; // No receipt found for this member
    }
} else {
    // If not logged in, redirect to the login page
    header('Location: ../../member/login.php');
    exit();
}

// Get the latest receipt number to pass to JavaScript
$latestReceiptNumber = isset($_SESSION['latestReceiptNumber']) ? $_SESSION['latestReceiptNumber'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt QR Code</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>
</head>
<body>
    <h2>Latest Receipt QR Code</h2>
    
    <!-- Container for QR Code -->
    <div id="qrcode"></div>

    <script>
        // Get the latest receipt number passed from PHP
        var latestReceiptNumber = <?php echo json_encode($latestReceiptNumber); ?>;

        // Check if a valid receipt number is available
        if (latestReceiptNumber) {
            // Generate the QR code using QRCode.js
            new QRCode(document.getElementById("qrcode"), {
                text: latestReceiptNumber,
                width: 128, // Width of the QR code
                height: 128, // Height of the QR code
                colorDark : "#000000", // Dark color of the QR code
                colorLight : "#ffffff", // Light color of the QR code
                correctLevel : QRCode.CorrectLevel.H // Error correction level
            });
        } else {
            document.getElementById("qrcode").innerHTML = "No receipt found.";
        }
    </script>
</body>
</html>