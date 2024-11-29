<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        #reader {
            width: 300px;
            margin: auto;
        }
        .message {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>QR Code Scanner</h1>
    <div id="reader"></div>
    <div class="message" id="message"></div>

    <script>
        const messageElement = document.getElementById("message");

        function showMessage(message, isSuccess = true) {
            messageElement.textContent = message;
            messageElement.style.color = isSuccess ? "green" : "red";
        }

        // Function to handle successful scans
        function onScanSuccess(decodedText) {
            showMessage("QR Code scanned: " + decodedText);

            // Send the decoded text to the server
            fetch('attendance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `receiptNumber=${decodedText}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        const action = data.action === "checkin" ? "checked in" : "checked out";
                        showMessage(`Success: You have been ${action}.`);
                    } else {
                        showMessage(data.message, false);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showMessage("An error occurred. Please try again.", false);
                });
        }

        // Function to handle scan errors
        function onScanFailure(error) {
            console.warn("QR Code scan error:", error);
        }

        // Initialize the QR Code scanner
        const html5QrCode = new Html5Qrcode("reader");

        // Start the scanner
        html5QrCode.start(
            { facingMode: "environment" }, // Use the back camera
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Error starting QR Code scanner:", err);
            showMessage("Unable to start QR scanner. Check browser permissions.", false);
        });
    </script>
</body>
</html>
