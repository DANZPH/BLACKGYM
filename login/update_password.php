<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Include the database connection (ensure connection.php defines $conn1)
include '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["token"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $token = $_POST["token"];
    $newPassword = $_POST["password"];

    // Check if user exists and token is valid
    $stmt = $conn1->prepare("SELECT * FROM Users WHERE Email = ? AND ResetToken = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        // Token is valid, proceed to update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password
        $stmt = $conn1->prepare("UPDATE Users SET Password = ?, ResetToken = NULL, ResetTokenExpiration = NULL WHERE Email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();
        $stmt->close();

        echo "Password updated successfully!";
    } else {
        echo "Invalid token or email.";
    }
} else {
    echo "Error: Email, token, and password are required.";
}

// No need to close $conn1 here if it's handled in connection.php
?>