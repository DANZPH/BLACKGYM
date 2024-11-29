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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        #reader {
            width: 300px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-top: 20px;
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

        function onScanSuccess(decodedText) {
            // Send the decoded text (ReceiptNumber) to the server
            fetch('../scanner.php', {
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

        function onScanFailure(error) {
            // Handle scan errors (optional)
            console.warn("QR Code scan error:", error);
        }

        // Initialize the QR Code scanner
        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" }, // Use the back camera
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        );
    </script>
</body>
</html>
