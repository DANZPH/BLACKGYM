<?php
// scanner.php

// Handle the POST request if QR data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qrData'])) {
    $qrData = $_POST['qrData'];

    // Log the QR code data to a file or process it (for example, save to a database)
    file_put_contents('qr_log.txt', "Scanned QR Code: $qrData\n", FILE_APPEND);

    // Optionally, you can send a response back to the frontend
    echo 'QR code data received: ' . htmlspecialchars($qrData);
    exit();  // Stop further execution (we already handled the request)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            text-align: center;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        #video {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        #scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80%;
            height: 80%;
            border: 2px solid rgba(255, 0, 0, 0.8);
            transform: translate(-50%, -50%);
            box-sizing: border-box;
            pointer-events: none;
        }

        #output {
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
            color: green;
        }

        #error {
            color: red;
            font-size: 1em;
        }
    </style>
</head>
<body>

<div id="scanner-container">
    <h1>QR Code Scanner</h1>
    <video id="video" autoplay></video>
    <div id="scanner-overlay"></div>
    <div id="output">Scan a QR code to see the result.</div>
    <div id="error"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    const video = document.getElementById('video');
    const output = document.getElementById('output');
    const errorDiv = document.getElementById('error');
    const scannerOverlay = document.getElementById('scanner-overlay');

    // Access the user's camera
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute('playsinline', true); // Important for iOS
                video.play();
                requestAnimationFrame(scanQRCode);
            })
            .catch(err => {
                console.error("Error accessing camera: " + err);
                errorDiv.innerText = 'Error accessing camera: ' + err;
            });
    } else {
        errorDiv.innerText = 'Your browser does not support camera access.';
    }

    function scanQRCode() {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        // Set canvas size equal to the video element
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;

        // Draw current video frame onto the canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Get image data from the canvas
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

        // Try to decode the QR code from the image data
        const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

        if (qrCode) {
            // QR code found, display the result
            output.innerText = 'QR Code Data: ' + qrCode.data;

            // Send the QR code data to the server (POST request)
            sendQRCodeData(qrCode.data);
        } else {
            // Keep scanning
            requestAnimationFrame(scanQRCode);
        }
    }

    function sendQRCodeData(data) {
        // Send the scanned QR code data to your server using AJAX (POST request)
        fetch('scanner.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'qrData=' + encodeURIComponent(data)
        })
        .then(response => response.text())
        .then(responseData => {
            console.log('Server Response: ' + responseData);
            // You can handle the server response here if needed
        })
        .catch(error => {
            console.error('Error sending QR data to server:', error);
        });
    }
</script>

</body>
</html>
