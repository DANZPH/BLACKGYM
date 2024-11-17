<?php

require '../login/phpmailer/src/Exception.php';
require '../login/phpmailer/src/PHPMailer.php';
require '../login/phpmailer/src/SMTP.php';
include '../database/connection.php'; // Include the connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST["email"]) &&
        isset($_POST["username"]) &&
        isset($_POST["password"]) &&
        isset($_POST["gender"]) &&
        isset($_POST["age"]) &&
        isset($_POST["address"]) &&
        isset($_POST["subscription"])
    ) {
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

            // Insert into Users table
            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, OTP, OTPExpiration, Verified) VALUES (?, ?, ?, ?, ?, ?)");
            $verified = 0;
            $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $otp, $otpExpiration, $verified);
            $stmt->execute();
            $userID = $stmt->insert_id;  // Get UserID
            $stmt->close();

            // Insert into Members table
            $stmt = $conn1->prepare("INSERT INTO Members (UserID, Gender, Age, Address, MembershipStatus) VALUES (?, ?, ?, ?, ?)");
            $membershipStatus = 'Inactive'; // Default membership status
            $stmt->bind_param("isiss", $userID, $gender, $age, $address, $membershipStatus);
            $stmt->execute();
            $memberID = $stmt->insert_id;  // Get MemberID
            $stmt->close();

            // Insert into Membership table
            $stmt = $conn1->prepare("INSERT INTO Membership (MemberID, Subscription, Status, StartDate) VALUES (?, ?, ?, NOW())");
            $status = 'Pending'; // Default membership status
            $stmt->bind_param("iss", $memberID, $subscription, $status);
            $stmt->execute();
            $membershipID = $stmt->insert_id;  // Get MembershipID
            $stmt->close();

            // Update MembershipID in Members table
            $stmt = $conn1->prepare("UPDATE Members SET MembershipID = ? WHERE MemberID = ?");
            $stmt->bind_param("ii", $membershipID, $memberID);
            $stmt->execute();
            $stmt->close();

            // Send OTP
            $result = sendOTP($email, $otp);

            if ($result === true) {
                echo "OTP sent to your email.";
            } else {
                echo "Error sending OTP: " . $result;
            }
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
        return $mail->ErrorInfo;
    }
}

?>