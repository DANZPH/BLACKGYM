<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <!-- Include the html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <style>
        #qr-reader {
            width: 100%; /* Full width of the container */
            height: 500px; /* Set a fixed height for the camera feed */
        }
    </style>
</head>
<body>

<h2>Scan a QR Code</h2>

<!-- Add a button to start scanning -->
<button id="startButton">Start Scanning</button>

<!-- Add a div for the QR code scanning -->
<div id="qr-reader"></div>

<!-- Display the decoded QR code data -->
<p id="result"></p>

<script>
    // Create an instance of Html5QrcodeScanner
    const qrCodeScanner = new Html5QrcodeScanner(
        "qr-reader", 
        {
            fps: 10, // Frames per second for scanning
            qrbox: 250 // Size of the scanning box that detects QR codes
        }, 
        true // Automatically request camera permission
    );

    // Start scanning when the button is clicked
    document.getElementById('startButton').addEventListener('click', function () {
        qrCodeScanner.render(onScanSuccess, onScanError);
    });

    // Success callback for QR code scan
    function onScanSuccess(decodedText, decodedResult) {
        // Display the decoded text
        document.getElementById('result').textContent = "QR Code Data: " + decodedText;

        // Optionally stop scanning after successful scan
        qrCodeScanner.clear();
    }

    // Error callback (optional)
    function onScanError(errorMessage) {
        console.warn("QR Code error: " + errorMessage);
    }
</script>

</body>
</html>
