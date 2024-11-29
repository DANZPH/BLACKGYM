<?php
session_start();
include '../../database/connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['MemberID'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$memberID = $_SESSION['MemberID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the scanned receipt number
    if (isset($_POST['receiptNumber'])) {
        $receiptNumber = $_POST['receiptNumber'];

        // Query to check if the receipt number exists in the database
        $query = "SELECT * FROM Payments WHERE ReceiptNumber = ? AND MemberID = ?";
        $stmt = $conn1->prepare($query);
        $stmt->bind_param("si", $receiptNumber, $memberID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $payment = $result->fetch_assoc();
            
            // Check attendance status (check-in or check-out)
            $attendanceQuery = "SELECT * FROM Attendance WHERE ReceiptNumber = ?";
            $attendanceStmt = $conn1->prepare($attendanceQuery);
            $attendanceStmt->bind_param("s", $receiptNumber);
            $attendanceStmt->execute();
            $attendanceResult = $attendanceStmt->get_result();

            if ($attendanceResult->num_rows == 0) {
                // First-time check-in
                $attendanceInsert = "INSERT INTO Attendance (ReceiptNumber, MemberID, status) VALUES (?, ?, 'checkin')";
                $attendanceInsertStmt = $conn1->prepare($attendanceInsert);
                $attendanceInsertStmt->bind_param("si", $receiptNumber, $memberID);
                $attendanceInsertStmt->execute();

                echo json_encode(["status" => "success", "action" => "checkin"]);
            } else {
                // Already checked in, now checkout
                $attendanceUpdate = "UPDATE Attendance SET status = 'checkout' WHERE ReceiptNumber = ?";
                $attendanceUpdateStmt = $conn1->prepare($attendanceUpdate);
                $attendanceUpdateStmt->bind_param("s", $receiptNumber);
                $attendanceUpdateStmt->execute();

                echo json_encode(["status" => "success", "action" => "checkout"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Receipt not found for this member."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No receipt number provided."]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        #video {
            width: 100%;
            max-width: 600px;
            height: auto;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Scan Your QR Code</h1>
    
    <video id="video" autoplay></video>
    <canvas id="canvas" style="display: none;"></canvas>

    <div class="message" id="message"></div>

    <script>
        let videoElement = document.getElementById("video");
        let canvasElement = document.getElementById("canvas");
        let messageElement = document.getElementById("message");

        // Set up the camera
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(function(stream) {
                videoElement.srcObject = stream;
                videoElement.setAttribute("playsinline", true);
                videoElement.play();
                requestAnimationFrame(scanQRCode);
            })
            .catch(function(err) {
                console.error("Camera access denied:", err);
                messageElement.textContent = "Unable to access camera.";
            });

        function scanQRCode() {
            // Set canvas size to match video element
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;

            // Draw current video frame on canvas
            canvasElement.getContext("2d").drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            // Scan the image for QR code
            let imageData = canvasElement.getContext("2d").getImageData(0, 0, canvasElement.width, canvasElement.height);
            let decoded = jsQR(imageData.data, imageData.width, imageData.height);

            if (decoded) {
                // Successfully decoded QR code
                handleQRCode(decoded.data);
            } else {
                // No QR code detected
                requestAnimationFrame(scanQRCode);
            }
        }

        function handleQRCode(receiptNumber) {
            messageElement.textContent = "QR Code scanned: " + receiptNumber;

            // Send the decoded receipt number to the backend
            fetch('scanner.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `receiptNumber=${receiptNumber}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    const action = data.action === "checkin" ? "Checked In" : "Checked Out";
                    messageElement.textContent = `Success: You have been ${action}.`;
                } else {
                    messageElement.textContent = `Error: ${data.message}`;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                messageElement.textContent = "An error occurred. Please try again.";
            });
        }
    </script>
</body>
</html>
