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
                /* Styling here as before */
            </style>
        </head>
        <body>

        <div class="container">
            <h2>Reset Password</h2>
            <form action="update_password.php" method="post">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <label for="password">Enter your new password:</label>
                <input type="password" id="password" name="password" required><br>
                <button type="submit">Reset Password</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.all.min.js"></script>
        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status'); // Check if there's a 'status' parameter

            if (status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your password has been reset successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        window.location.href = 'login.php'; // Redirect to login page after success
                    }
                });
            } else if (status === 'error') {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (status === 'invalid') {
                Swal.fire({
                    title: 'Invalid Token!',
                    text: 'The token or email is invalid.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        </script>

        </body>
        </html>

        <?php
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>