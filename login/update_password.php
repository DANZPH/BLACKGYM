<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Database credentials
$host = "sql104.infinityfree.com"; // Change this to your database host
$dbname = "if0_36048499_db_user"; // Change this to your database name
$usernameDB = "if0_36048499"; // Change this to your database username
$passwordDB = "LokK4Hhvygq"; // Change this to your database password

// Create connection
$conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["token"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $token = $_POST["token"];
    $newPassword = $_POST["password"];

    // Check if user exists and token is valid
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ? AND ResetToken = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        // Token is valid, proceed to update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password
        $stmt = $conn->prepare("UPDATE Users SET Password = ?, ResetToken = NULL, ResetTokenExpiration = NULL WHERE Email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();
        $stmt->close();

        // Redirect with success message
        header("Location: reset_password.php?email=$email&status=success");
        exit();
    } else {
        // Invalid token or email
        header("Location: reset_password.php?email=$email&status=error");
        exit();
    }
} else {
    // Missing fields
    header("Location: reset_password.php?status=error");
    exit();
}

$conn->close(); // Close the database connection after use

?>