<?php
session_start();

require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';
include '../../database/connection.php'; // Include the connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $gender = $_POST["gender"];
        $age = $_POST["age"];
        $address = $_POST["address"];
        $membershipType = $_POST["membershipType"];
        $subscriptionMonths = $_POST["subscriptionMonths"] ?? null;
        $sessionPrice = $_POST["sessionPrice"] ?? null;
        $balance = $_POST["balance"] ?? 0;

        // Set timezone to Asia/Manila
        date_default_timezone_set('Asia/Manila');
        $createdAt = date('Y-m-d H:i:s');

        // Check if email is already registered
        $stmt = $conn1->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            echo "Email already registered.";
        } else {
            $resetToken = bin2hex(random_bytes(32));
            $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hour'));

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into Users table
            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, ResetToken, ResetTokenExpiration, Verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $verified = 0;
            $stmt->bind_param("sssssis", $username, $email, $hashedPassword, $resetToken, $resetTokenExpiration, $verified, $createdAt);
            $stmt->execute();
            $userID = $stmt->insert_id;
            $stmt->close();

            // Insert into Members table
            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus, Balance, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $membershipStatus = 'Inactive';
            $stmt->bind_param("isissds", $userID, $gender, $age, $address, $membershipStatus, $balance, $createdAt);
            $stmt->execute();
            $memberID = $stmt->insert_id;
            $stmt->close();

            // Insert into Membership table
            if ($membershipType === 'Subscription') {
                $startDate = date('Y-m-d H:i:s');
                $endDate = NULL; // Setting EndDate to NULL
                $subscriptionAmount = 600.00 * $subscriptionMonths;

                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, Subscription, Status, StartDate, EndDate) VALUES (?, ?, ?, ?, ?)");
                $status = 'Pending';
                $stmt->bind_param("idsss", $memberID, $subscriptionAmount, $status, $startDate, $endDate);
                $stmt->execute();
                $stmt->close();
            } elseif ($membershipType === 'SessionPrice') {
                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, SessionPrice, Status) VALUES (?, ?, ?)");
                $status = 'Pending';
                $stmt->bind_param("ids", $memberID, $sessionPrice, $status);
                $stmt->execute();
                $stmt->close();
            }

            // Send Reset Link
            $result = sendResetLink($email, $resetToken);
            echo $result === true ? "Verification link sent." : "Succes sending verification email: " . $result;
        }
    } else {
        echo "Error: Email, username, and password are required.";
    }
}

function sendResetLink($email, $resetToken) {
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
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #333333;
                font-size: 24px;
                margin-bottom: 10px;
            }
            p {
                color: #555555;
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .cta-button {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                color: #ffffff;
                background-color: #007BFF;
                text-decoration: none;
                border-radius: 4px;
                text-align: center;
            }
            .cta-button:hover {
                background-color: #0056b3;
            }
            .footer {
                font-size: 12px;
                color: #888888;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Hello,</h2>
            <p>Thank you for registering at <strong>Black Gym</strong>. Please verify your email address by clicking the button below:</p>
            <a href='https://dazx.xyz/login/auth.php?email=$email&token=$resetToken' class='cta-button'>Verify Your Email</a>
            <p>This link will expire in 24 hours. However, you can always access your account by <a href='https://dazx.xyz/login/auth.php?email=$email&token=$resetToken'>clicking here</a>.</p>
            <p>Your default password is: <strong>'member'</strong>. You can change your password through your profile settings after logging in.</p>
        </div>
        <div class='footer'>
            <p>Best regards, <br> The Black Gym Team</p>
        </div>
    </body>
    </html>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
?>