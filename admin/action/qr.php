<?php
include '../../database/connection.php';
require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

// Fetch the receipt number from the database
$query = "SELECT ReceiptNumber FROM Payments ORDER BY PaymentDate DESC LIMIT 1";
$result = $conn1->query($query);

if (!$result) {
    die("Query failed: " . $conn1->error);
}

$row = $result->fetch_assoc();
if (!$row) {
    die("No receipt found.");
}

$receiptNumber = $row['ReceiptNumber'];

// Generate the QR code
$qrCode = new QrCode($receiptNumber);
$qrCode->setSize(300); // Set the size of the QR code
$qrCode->setMargin(10); // Set the margin around the QR code
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH); // Set error correction level

// Save the QR code as a PNG file
$writer = new PngWriter();
$qrImagePath = 'qr_code.png';
$writer->writeFile($qrCode, $qrImagePath);

// Send the QR code via email using PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mail.blackgym@gmail.com'; // Your Gmail
    $mail->Password = 'akbbhmrrxzryovqt'; // Your Gmail app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Sender and recipient
    $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
    $mail->addAddress($email); // Recipient's email address

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Verification Code for Black Gym Account';
    $mail->Body = "Dear User,<br><br>Here is your verification QR code for the Black Gym account.<br><br>Best Regards,<br>Black Gym";

    // Attach the QR code image
    $mail->addAttachment($qrImagePath, 'verification_code.png'); // Attach the QR code image

    // Send the email
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Optional: Delete the QR code image file after sending the email (for cleanup)
unlink($qrImagePath);
?>