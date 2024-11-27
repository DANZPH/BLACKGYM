<?php
session_start();  // Start the session to store session variables
include '../../database/connection.php';  // Include the database connection

// Check if the user is logged in
if (isset($_SESSION['MemberID'])) {
    $memberID = $_SESSION['MemberID']; // Retrieve the MemberID from the session

    // Query to fetch the latest ReceiptNumber for the logged-in member
    $receipt_query = $conn1->prepare("SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1");
    $receipt_query->bind_param("i", $memberID);
    $receipt_query->execute();
    $receipt_result = $receipt_query->get_result();

    // Check if a receipt is found
    if ($receipt_result->num_rows > 0) {
        $receipt = $receipt_result->fetch_assoc();
        $_SESSION['latestReceiptNumber'] = $receipt['ReceiptNumber']; // Store the latest receipt number in session
    } else {
        $_SESSION['latestReceiptNumber'] = null; // No receipt found for this member
    }
} else {
    // If not logged in, redirect to the login page
    header('Location: ../../member/login.php');
    exit();
}

// Fetch the latest receipt number
$latestReceiptNumber = isset($_SESSION['latestReceiptNumber']) ? $_SESSION['latestReceiptNumber'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display QR Code</title>
    <!-- Include qrcode.js -->
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    
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
            text-align: center;  /* Center all content inside */
        }

        h2 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        #qrcode {
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }

        .download-btn {
            display: block;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .download-btn:hover {
            background-color: #45a049;
        }

        .message {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Display a message if no receipt number is found
    if ($latestReceiptNumber) {
        echo "<h2>Latest Receipt Number: $latestReceiptNumber</h2>";
    } else {
        echo "<h2>No receipt found for this member.</h2>";
    }
    ?>

    <!-- Custom message -->
    <div class="message">
        <?php echo "This is Your GYMPASS and ATTENDANCE QRcode"; ?>
    </div>

    <!-- Div to hold the generated QR Code -->
    <div id="qrcode"></div>

    <!-- Download QR Code Button -->
    <a href="#" id="download-btn" class="download-btn" style="display:none;">Download QR</a>
</div>

<script type="text/javascript">
    // Generate the QR code if there's a receipt number
    var receiptNumber = "<?php echo $latestReceiptNumber; ?>";
    if (receiptNumber) {
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: receiptNumber,  // Set the text to the receipt number
            width: 300,           // Width of the QR code
            height: 300,          // Height of the QR code
            colorDark: "#000000", // Dark color
            colorLight: "#ffffff", // Light color
            correctLevel: QRCode.CorrectLevel.H // Error correction level
        });

        // Show the download button after QR code is generated
        var downloadBtn = document.getElementById("download-btn");
        downloadBtn.style.display = "block";

        // Allow the user to download the QR code as an image
        downloadBtn.addEventListener("click", function() {
            var canvas = document.querySelector("canvas");  // Get the canvas element of the QR code
            if (canvas) {
                var imgData = canvas.toDataURL("image/png");  // Get image data as base64 PNG
                var link = document.createElement("a");
                link.href = imgData;
                link.download = "gympass_qrcode.png";  // Name of the downloaded file
                link.click();
            }
        });
    }
</script>

</body>
</html>