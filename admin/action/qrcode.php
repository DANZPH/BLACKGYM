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
        fetch('getReceiptNumber.php')  // Assuming 'getReceiptNumber.php' will return the receipt number
            .then(response => response.json()) // Expecting JSON data from the PHP backend
            .then(data => {
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