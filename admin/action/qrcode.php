<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
    <h1>Generated QR Code</h1>
    
    <!-- The <img> element where the QR code will be rendered -->
    <img id="qr-code-img" alt="QR Code" />

    <script>
        // Fetch the latest receipt number from the backend
        fetch('qr.php')  // Assuming 'getReceiptNumber.php' will return the receipt number
            .then(response => response.json()) // Expecting JSON data from the PHP backend
            .then(data => {
                const receiptNumber = data.receiptNumber;
                
                // Generate the QR code and set it as the src of the img element
                QRCode.toDataURL(receiptNumber, { errorCorrectionLevel: 'H' }, function (error, url) {
                    if (error) {
                        console.error('Error generating QR code:', error);
                    } else {
                        // Set the generated QR code as the image source
                        document.getElementById('qr-code-img').src = url;
                    }
                });
            })
            .catch(error => {
                console.error("Error fetching receipt number:", error);
            });
    </script>
</body>
</html>