<?php
session_start();

// Check if the member is logged in
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in
    header('Location: ../../member/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <!-- Use an updated working JSQR library -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        #qrText {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
            font-weight: 600;
        }

        #scannerCanvas {
            display: none;
        }

        video {
            width: 100%;
            height: auto;
            max-height: 500px; /* Prevent stretching */
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>QR Code Scanner</h2>

    <div id="qrText">Scan your QR code to check-in/check-out.</div>

    <video id="videoElement" autoplay></video>

    <canvas id="scannerCanvas"></canvas>

</div>

<script>
// Initialize the video element and canvas
const videoElement = document.getElementById('videoElement');
const qrTextElement = document.getElementById('qrText');

// Initialize the video stream from the user's camera
navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then(function (stream) {
        videoElement.srcObject = stream;
        videoElement.setAttribute('playsinline', true); // Required for iOS
        videoElement.play();
        requestAnimationFrame(scanQRCode);
    })
    .catch(function (error) {
        qrTextElement.textContent = 'Error accessing camera: ' + error;
        console.error(error);
    });

// Function to scan QR code from the video stream
function scanQRCode() {
    if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
        // Ensure the video dimensions are available
        if (videoElement.videoWidth > 0 && videoElement.videoHeight > 0) {
            // Draw current video frame on canvas
            const canvasElement = document.createElement('canvas');
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            const ctx = canvasElement.getContext("2d");
            ctx.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            // Scan the image for QR code
            let imageData = ctx.getImageData(0, 0, canvasElement.width, canvasElement.height);
            let decoded = jsQR(imageData.data, imageData.width, imageData.height);

            if (decoded) {
                // Successfully decoded QR code, display the result
                qrTextElement.textContent = "Decoded QR: " + decoded.data;
            } else {
                requestAnimationFrame(scanQRCode); // Continue scanning
            }
        } else {
            console.warn("Video dimensions are not available yet. Retrying...");
            requestAnimationFrame(scanQRCode); // Retry if dimensions are not yet set
        }
    } else {
        console.warn("Video stream not ready. Retrying...");
        requestAnimationFrame(scanQRCode); // Retry if video is not ready
    }
}
</script>

</body>
</html>
