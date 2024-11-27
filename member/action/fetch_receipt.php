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

// Fetch the latest receipt number
$latestReceiptNumber = isset($_SESSION['latestReceiptNumber']) ? $_SESSION['latestReceiptNumber'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display QR Code</title>
    <!-- Include qrcode.js -->
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>
<body>

<?php
// Display a message if no receipt number is found
if ($latestReceiptNumber) {
    echo "<h2>Latest Receipt Number: $latestReceiptNumber</h2>";
} else {
    echo "<h2>No receipt found for this member.</h2>";
}
?>

<!-- Div to hold the generated QR Code -->
<div id="qrcode">
</div>

<script type="text/javascript">
    // Generate the QR code if there's a receipt number
    var receiptNumber = "<?php echo $latestReceiptNumber; ?>";
    if (receiptNumber) {
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: receiptNumber,  // Set the text to the receipt number
            width: 500,           // Width of the QR code
            height: 500,          // Height of the QR code
            colorDark : "#000000", // Dark color
            colorLight : "#ffffff", // Light color
            correctLevel : QRCode.CorrectLevel.H // Error correction level
        });
    }
</script>

</body>
</html>