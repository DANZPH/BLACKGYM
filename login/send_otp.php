<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include '../database/connection.php'; // Include the connection file without internal SQL connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set the default timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email, username, and password are set
    if (isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $gender = $_POST["gender"];
        $age = $_POST["age"];
        $address = $_POST["address"];
        $membershipType = $_POST["membershipType"];
        $subscriptionMonths = isset($_POST["subscriptionMonths"]) ? $_POST["subscriptionMonths"] : null;
        $sessionPrice = isset($_POST["sessionPrice"]) ? $_POST["sessionPrice"] : null;

        // Check if email is already registered
        $stmt = $conn1->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // Email already registered
            echo "Email already registered.";
        } else {
            // Email not registered, proceed with registration and OTP sending
            $otp = generateOTP();
            $otpExpiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));  // OTP expires in 15 minutes

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert email, username, hashed password, OTP, and OTP expiration into the Users table
            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, OTP, OTPExpiration, Verified) VALUES (?, ?, ?, ?, ?, ?)");
            $verified = 0; // Set the user as not verified
            $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $otp, $otpExpiration, $verified);
            $stmt->execute();
            $userID = $stmt->insert_id;  // Get the inserted user ID
            $stmt->close();

            // Insert the user into the Members table with default status 'Inactive'
            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus) VALUES (?, ?, ?, ?, ?)");
            $membershipStatus = 'Inactive';  // Default membership status is 'Inactive'
            $stmt->bind_param("isiss", $userID, $gender, $age, $address, $membershipStatus);
            $stmt->execute();
            $memberID = $stmt->insert_id;  // Get the inserted member ID
            $stmt->close();

            // Insert the user into the Membership table based on their membership choice
            if ($membershipType === 'Subscription') {
                // For Subscription, calculate the end date based on months
                $startDate = date('Y-m-d H:i:s');
                $endDate = date('Y-m-d H:i:s', strtotime("+$subscriptionMonths months"));
                
                // Insert Subscription details into Membership table
                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, Subscription, Status, StartDate, EndDate) VALUES (?, ?, ?, ?, ?)");
                $status = 'Pending';  // Default status is 'Pending'
                $subscriptionAmount = 600.00 * $subscriptionMonths; // Example: 600 per month, calculate total
                $stmt->bind_param("idsss", $memberID, $subscriptionAmount, $status, $startDate, $endDate);
                $stmt->execute();
                $stmt->close();
            } else if ($membershipType === 'SessionPrice') {
                // For Pay Per Session, insert session price into the Membership table
                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, SessionPrice, Status) VALUES (?, ?, ?)");
                $status = 'Active';  // Default status is 'Active' for Pay Per Session
                $stmt->bind_param("ids", $memberID, $sessionPrice, $status);
                $stmt->execute();
                $stmt->close();
            }

            // Send OTP via email
            $result = sendOTP($email, $otp);

            if ($result === true) {
                echo "OTP sent to your email.";
            } else {
                echo "Error sending OTP: " . $result;
            }
        }
    } else {
        echo "Error: Email, username, and password are required.";
    }
}

function generateOTP() {
    // Generate a 6-digit random OTP
    return sprintf('%06d', mt_rand(0, 999999));
}

function sendOTP($email, $otp) {
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
        $mail->Subject = 'Verification Code for Black Gym Account';
        
        // Professional and clear email body with better formatting
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
                .otp-code {
                    font-size: 24px;
                    font-weight: bold;
                    color: #333;
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
                    <h3>Black Gym Account Verification</h3>
                </div>
                <p>Hello,</p>
                <p>Thank you for choosing Black Gym. To complete your registration or verification process, please use the following code:</p>
                <div class='otp-code'>
                    $otp
                </div>
                <p>This code is valid for the next 15 minutes. If you did not request this verification code, please ignore this email.</p>
                <div class='footer'>
                    <p>If you have any questions or need further assistance, please don't hesitate to <a href='mailto:support@blackgym.com'>contact our support team</a>.</p>
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