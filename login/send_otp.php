<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include '../database/connection.php'; // Include the connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $mail->Username = 'kentdancel20@gmail.com'; // Your Gmail
        $mail->Password = 'nrgtyaqgymoadryg'; // Your Gmail app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('kentdancel20@gmail.com'); // Your Gmail
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
        $mail->Body = 'Your verification code is: ' . $otp;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}

?>