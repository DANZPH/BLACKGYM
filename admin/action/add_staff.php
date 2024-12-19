<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';
include '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $jobTitle = $_POST['jobTitle']; // Staff-specific field (Job Title)

    // Check if the email is already registered
    $stmt = $conn1->prepare("SELECT * FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email already registered.";
    } else {
        // Generate Reset Token
        $resetToken = bin2hex(random_bytes(32));
        $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Insert user data into Users table
        $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, ResetToken, ResetTokenExpiration, Verified) VALUES (?, ?, ?, ?, ?, 0)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssss", $username, $email, $hashedPassword, $resetToken, $resetTokenExpiration);
        $stmt->execute();
        $userId = $stmt->insert_id; // Get the newly inserted UserID

        // Insert staff-specific data into Staff table
        $stmt = $conn1->prepare("INSERT INTO Staff (UserID, JobTitle) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $jobTitle);
        $stmt->execute();

        // Send Reset Token Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mail.blackgym@gmail.com';
            $mail->Password = 'akbbhmrrxzryovqt';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email - Black Gym';
            $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
            color: #555;
        }
        .content a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .content a:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h1>Welcome to Black Gym!</h1>
        </div>
        <div class='content'>
            <p>Hello,</p>
            <p>Thank you for registering as a staff member at Black Gym. To verify your email, please click the button below:</p>
            <a href='https://dazx.xyz/login/auth.php?email=$email&token=$resetToken'>Verify Your Email</a>
            <p>This link will expire in 1 hour. If you didn’t register at Black Gym, you can safely ignore this email.</p>
        </div>
        <div class='footer'>
            <p>Black Gym &copy; 2024. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";

            $mail->send();
            echo "Verification link sent.";
        } catch (Exception $e) {
            echo "Error sending verification email: " . $mail->ErrorInfo;
        }
    }
    $stmt->close();
}
?>