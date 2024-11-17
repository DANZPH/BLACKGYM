<?php

require '../login/phpmailer/src/Exception.php';
require '../login/phpmailer/src/PHPMailer.php';
require '../login/phpmailer/src/SMTP.php';
include '../database/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"], $_POST["username"], $_POST["password"], $_POST["gender"], $_POST["age"], $_POST["address"], $_POST["subscription"])) {
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $gender = $_POST["gender"];
        $age = $_POST["age"];
        $address = $_POST["address"];
        $subscription = $_POST["subscription"];

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

            // Insert user into Users table
            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, OTP, OTPExpiration, Verified) VALUES (?, ?, ?, ?, ?, ?)");
            $verified = 0;
            $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $otp, $otpExpiration, $verified);
            $stmt->execute();
            $userID = $stmt->insert_id;
            $stmt->close();

            // Insert into Members table
            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus) VALUES (?, ?, ?, ?, 'Inactive')");
            $stmt->bind_param("isis", $userID, $gender, $age, $address);
            $stmt->execute();
            $memberID = $stmt->insert_id;
            $stmt->close();

            // Insert into Membership table
            $startDate = date('Y-m-d');
            $endDate = $subscription === 'monthly' ? date('Y-m-d', strtotime('+1 month')) : null;
            $stmt = $conn1->prepare("INSERT INTO Membership (Subscription, Status, StartDate, EndDate) VALUES (?, 'Active', ?, ?)");
            $stmt->bind_param("sss", $subscription, $startDate, $endDate);
            $stmt->execute();
            $stmt->close();

            // Send OTP
            $result = sendOTP($email, $otp);
            echo $result === true ? "OTP sent to your email." : "Error sending OTP: " . $result;
        }
    } else {
        echo "Error: All fields are required.";
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
        $mail->Username = 'kentdancel20@gmail.com';
        $mail->Password = 'nrgtyaqgymoadryg';
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