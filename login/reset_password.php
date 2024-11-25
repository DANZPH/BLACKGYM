 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

//require '../database/connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is set
    if (isset($_POST["email"])) {
        $email = $_POST["email"];

        // Database connection (adjust according to your setup)
        $host = "sql104.infinityfree.com"; // Change this to your database host
        $dbname = "if0_36048499_db_user"; // Change this to your database name
        $usernameDB = "if0_36048499"; // Change this to your database username
        $passwordDB = "LokK4Hhvygq"; // Change this to your database password

        $conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if user exists in the database
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // User exists, generate reset token and expiration time
            $resetToken = generateResetToken();
            $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Token expiration time (1 hour from now)

            // Update the reset token and expiration time in the Users table
            $stmt = $conn->prepare("UPDATE Users SET ResetToken = ?, ResetTokenExpiration = ? WHERE Email = ?");
            $stmt->bind_param("sss", $resetToken, $resetTokenExpiration, $email);
            $stmt->execute();
            $stmt->close();

            // Send reset link via email
            $result = sendResetEmail($email, $resetToken);

            if ($result === true) {
                echo "Its take a time to send Reset link sent to your email. please wait";
            } else {
                echo "Error sending reset link: " . $result;
            }
        } else {
            echo "Error: Email not found.";
        }

        $conn->close();
    } else {
        echo "Error: Email is required.";
    }
}

function generateResetToken() {
    // Generate a random reset token
    return bin2hex(random_bytes(32));  // 64 characters long token
}

function sendResetEmail($email, $resetToken) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com'; // Your Gmail
        $mail->Password = 'akbbhmrrxzryovqt'; // Your Gmail app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym'); // Your Gmail
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request for Black Gym Account';
        
        // Professional and polished email body
        $mail->Body = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    color: #333;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .container {
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    width: 100%;
                    max-width: 600px;
                    margin: auto;
                }
                .header {
                    text-align: center;
                    font-size: 22px;
                    color: #1a73e8;
                    margin-bottom: 20px;
                }
                .reset-link {
                    font-size: 18px;
                    font-weight: bold;
                    color: #007bff;
                    text-decoration: none;
                    display: block;
                    margin: 20px 0;
                    text-align: center;
                    padding: 10px;
                    background-color: #e0f7fa;
                    border-radius: 5px;
                }
                .footer {
                    font-size: 14px;
                    text-align: center;
                    color: #555;
                    margin-top: 20px;
                }
                .footer a {
                    color: #1a73e8;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h3>Password Reset Request for Your Black Gym Account</h3>
                </div>
                <p>Hello,</p>
                <p>We received a request to reset the password for your Black Gym account. If you did not request this, please ignore this email.</p>
                <p>To reset your password, please click the link below:</p>
                <a href='https://beta.dazx.xyz/login/reset.php?email=$email&token=$resetToken' class='reset-link'>
                    Reset Your Password
                </a>
                <p>This link will expire in 30 minutes. If you don't reset your password within that time, you will need to request a new password reset.</p>
                <div class='footer'>
                    <p>If you have any questions or need assistance, please don't hesitate to <a href='mailto:support@blackgym.com'>contact our support team</a>.</p>
                    <p>Best regards, <br>Black Gym Team</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}