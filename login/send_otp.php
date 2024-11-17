<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include '../database/connection.php'; // Include the connection file without internal SQL connection

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
        $subscriptionMonths = isset($_POST["subscriptionMonths"]) ? intval($_POST["subscriptionMonths"]) : 0;
        $sessionPrice = isset($_POST["sessionPrice"]) ? floatval($_POST["sessionPrice"]) : null;

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

            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus) VALUES (?, ?, ?, ?, ?)");
            $membershipStatus = 'Inactive';
            $stmt->bind_param("isiss", $userID, $gender, $age, $address, $membershipStatus);
            $stmt->execute();
            $memberID = $stmt->insert_id;
            $stmt->close();

            if ($membershipType === 'Subscription') {
                if ($subscriptionMonths > 0) {
                    $startDate = date('Y-m-d H:i:s');

                    // Corrected method to calculate the `EndDate`
                    $endDate = (new DateTime($startDate))
                        ->modify("+$subscriptionMonths months")
                        ->format('Y-m-d H:i:s');

                    $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, Subscription, Status, StartDate, EndDate) VALUES (?, ?, ?, ?, ?)");
                    $status = 'Pending';
                    $subscriptionAmount = 600.00 * $subscriptionMonths;
                    $stmt->bind_param("idsss", $memberID, $subscriptionAmount, $status, $startDate, $endDate);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Invalid number of months. Please enter a valid subscription duration.";
                    exit;
                }
            } else if ($membershipType === 'SessionPrice') {
                $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, SessionPrice, Status) VALUES (?, ?, ?)");
                $status = 'Active';
                $stmt->bind_param("ids", $memberID, $sessionPrice, $status);
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
        $mail->Username = ''; // Your Gmail
        $mail->Password = 'nrgtyaqgymoadryg'; // Your Gmail app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('kentdancel20@gmail.com');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
        $mail->Body = 'Your verification code is: ' . $otp;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
?>