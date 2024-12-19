<?php
session_start();

require '../../phpmailer/src/Exception.php';
require '../../phpmailer/src/PHPMailer.php';
require '../../phpmailer/src/SMTP.php';
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
            $otp = generateOTP();
            $otpExpiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into Users table
            $stmt = $conn1->prepare("INSERT INTO Users (Username, Email, Password, OTP, OTPExpiration, Verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $verified = 0;
            $stmt->bind_param("sssssis", $username, $email, $hashedPassword, $otp, $otpExpiration, $verified, $createdAt);
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
                $endDate = date('Y-m-d H:i:s', strtotime("+$subscriptionMonths months"));
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

            // Send OTP
            $result = sendOTP($email, $otp);
            echo $result === true ? "OTP sent to your email." : "Error sending OTP: " . $result;
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
            <p>Use the following code to verify your Black Gym account:</p>
            <h1>$otp</h1>
            <p>This code will expire in 15 minutes.</p>
        </body>
        </html>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
