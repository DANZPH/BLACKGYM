<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code</title>
    <!-- Include QRCode.js library from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
    <h1>Generate QR Code for Receipt</h1>

    <!-- Element to display the QR Code -->
    <div id="qrcode"></div>

    <script>
        // Fetch the latest receipt number stored in the session using PHP
        <?php
        // Start session and retrieve the latest receipt number
        session_start();
        $receiptNumber = isset($_SESSION['latestReceiptNumber']) ? $_SESSION['latestReceiptNumber'] : null;
        ?>

        // Ensure the receipt number exists
        const receiptNumber = "<?php echo $receiptNumber; ?>";

        if (receiptNumber) {
            // Use QRCode.js to generate the QR code
            QRCode.toCanvas(document.getElementById("qrcode"), receiptNumber, function (error) {
                if (error) {
                    console.error("Error generating QR code:", error);
                }
            });
        } else {
            // If no receipt number is found, display an error message
            document.getElementById("qrcode").innerHTML = "No receipt found for this member.";
        }
    </script>
</body>
</html>