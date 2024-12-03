<?php

require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';
include '../../database/connection.php'; // Include the connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            echo "Email already registered.";
        } else {
            $otp = generateOTP();
            $otpExpiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, OTP, OTPExpiration, Verified) VALUES (?, ?, ?, ?, ?, ?)");
            $verified = 0;
            $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $otp, $otpExpiration, $verified);
            $stmt->execute();
            $userID = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $membershipStatus = 'Inactive';
            $createdAt = date('Y-m-d H:i:s'); // Use Asia/Manila timezone
            $stmt->bind_param("isisss", $userID, $gender, $age, $address, $membershipStatus, $createdAt);
            $stmt->execute();
            $memberID = $stmt->insert_id;
            $stmt->close();

            if ($membershipType === 'Subscription') {
                $startDate = $createdAt;
                $endDate = date('Y-m-d H:i:s', strtotime("+$subscriptionMonths months"));

                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, Subscription, Status, StartDate, EndDate, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $status = 'Pending';
                $subscriptionAmount = 600.00 * $subscriptionMonths;
                $stmt->bind_param("idssss", $memberID, $subscriptionAmount, $status, $startDate, $endDate, $createdAt);
                $stmt->execute();
                $stmt->close();
            } else if ($membershipType === 'SessionPrice') {
                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, SessionPrice, Status, created_at) VALUES (?, ?, ?, ?)");
                $status = 'Active';
                $stmt->bind_param("idss", $memberID, $sessionPrice, $status, $createdAt);
                $stmt->execute();
                $stmt->close();
            }

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
    return sprintf('%06d', mt_rand(0, 999999));
}

function sendOTP($email, $otp) {
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
        $mail->Subject = 'Verification Code for Black Gym Account';
        $mail->Body = "
        <html>
        <body>
            <p>Hello,</p>
            <p>Your OTP is: <b>$otp</b>. It is valid for the next 15 minutes.</p>
            <p>Thank you for registering with Black Gym!</p>
        </body>
        </html>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}