<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <style>
        #scanner-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }
        #video {
            width: 100%;
            height: auto;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        #output {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div id="scanner-container">
    <h1>QR Code Scanner</h1>
    <video id="video" autoplay></video>
    <div id="output">Scan a QR code to see the result.</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    // Get video element and canvas to capture frames from video feed
    const video = document.getElementById('video');
    const output = document.getElementById('output');

    // Check if browser supports video
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                requestAnimationFrame(scanQRCode);
            })
            .catch(err => {
                console.error("Error accessing camera: " + err);
                output.innerText = 'Camera error: ' + err;
            });
    } else {
        output.innerText = 'Your browser does not support camera access.';
    }

    function scanQRCode() {
        // Create a canvas to extract the current video frame
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
            // If QR code is found, display the result
            output.innerText = 'QR Code Data: ' + qrCode.data;
            // Optionally, send the result to your server
            sendQRCodeData(qrCode.data);
        } else {
            // Keep scanning
            requestAnimationFrame(scanQRCode);
        }
    }

    function sendQRCodeData(data) {
        // Send the scanned QR code data to your server using AJAX (Optional)
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
        })
        .catch(error => {
            console.error('Error sending QR data to server:', error);
        });
    }
</script>

</body>
</html>
