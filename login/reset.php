<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Database credentials
$host = "sql104.infinityfree.com"; // Change this to your database host
$dbname = "if0_36048499_db_user"; // Change this to your database name
$usernameDB = "if0_36048499"; // Change this to your database username
$passwordDB = "LokK4Hhvygq"; // Change this to your database password

// Create connection
$conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["email"]) && isset($_GET["token"])) {
    $email = $_GET["email"];
    $token = $_GET["token"];

    // Check if user exists and token is valid
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ? AND ResetToken = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        // User exists and token is valid, show password reset form
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
            <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .container {
                    background-color: #fff;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 400px;
                }
                h2 {
                    text-align: center;
                    color: #333;
                    margin-bottom: 20px;
                }
                label {
                    font-size: 16px;
                    color: #333;
                    display: block;
                    margin-bottom: 8px;
                }
                input[type="password"] {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 20px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    font-size: 16px;
                    box-sizing: border-box;
                }
                button {
                    width: 100%;
                    padding: 12px;
                    background-color: #1a73e8;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }
                button:hover {
                    background-color: #0c63d2;
                }
            </style>
        </head>
        <body>

        <div class="container">
            <h2>Reset Password</h2>
            <form action="update_password.php" method="post" id="resetPasswordForm">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <label for="password">Enter your new password:</label>
                <input type="password" id="password" name="password" required><br>
                <button type="submit">Reset Password</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.all.min.js"></script>
<script>
    // Add SweetAlert for success/error alerts based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status'); // check if there's a 'status' parameter

    if (status === 'success') {
        Swal.fire({
            title: 'Success!',
            text: 'Your password has been reset successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } else if (status === 'error') {
        Swal.fire({
            title: 'Error!',
            text: 'Something went wrong. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
</script>
        </body>
        </html>

        <?php
    } else {
        // Invalid token or email
        echo "Invalid token or email.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();  // Close the database connection after use
?>