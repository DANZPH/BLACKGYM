<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

require_once '../../vendor/autoload.php'; // Ensure the autoloader is included
include '../../database/connection.php'; 
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

function sendReceiptEmail($email, $name, $qrCodeImageData) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt'; // Update this with your SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful';
        $mail->Body = "<p>Dear $name,</p><p>Please find your payment receipt and QR code attached.</p><p>Thank you for being a member of Black Gym!</p>";

        // Attach QR code image directly as an attachment
        $mail->addStringAttachment($qrCodeImageData, 'QRCode.png', 'base64', 'image/png');

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

function processPayment($memberID, $paymentType, $amount, $amountPaid) {
    global $conn1;

    $changeAmount = $amountPaid - $amount;
    $receiptNumber = uniqid('RCT-');
    $paymentDate = date('Y-m-d H:i:s');

    if ($amountPaid < $amount) {
        throw new Exception("Amount paid cannot be less than the amount.");
    }

    $conn1->begin_transaction();

    // Insert payment into the database
    $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount, ReceiptNumber, PaymentDate) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddsss", $memberID, $paymentType, $amount, $amountPaid, $changeAmount, $receiptNumber, $paymentDate);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting payment: " . $stmt->error);
    }

    // Update membership status
    $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
    $updateMemberStmt->bind_param("d", $memberID);
    if (!$updateMemberStmt->execute()) {
        throw new Exception("Error updating membership status: " . $updateMemberStmt->error);
    }

    // Update membership end date
    $subscription = $amount;
    $numMonths = floor($subscription / 600);
    $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));

    $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?");
    $updateMembershipStmt->bind_param("sd", $endDate, $memberID);
    if (!$updateMembershipStmt->execute()) {
        throw new Exception("Error updating membership: " . $updateMembershipStmt->error);
    }

    // Fetch email and name for the member
    $emailQuery = $conn1->prepare("SELECT Users.Email, Users.Username AS Name FROM Users 
                                   INNER JOIN Members ON Users.UserID = Members.UserID 
                                   WHERE Members.MemberID = ?");
    $emailQuery->bind_param("d", $memberID);
    $emailQuery->execute();
    $emailQuery->bind_result($email, $name);
    $emailQuery->fetch();
    $emailQuery->close();

    // Generate QR code content (use receiptNumber as the content for the QR code)
    $builder = new Builder(
        writer: new PngWriter(),
        data: $receiptNumber,  // Use receiptNumber as content for the QR code
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10
    );

    $qrCodeResult = $builder->build();
    $qrCodeImageData = $qrCodeResult->getString();  // Get the QR code image as raw data

    // Send email with the QR code attached
    sendReceiptEmail($email, $name, $qrCodeImageData);

    // Commit the transaction
    $conn1->commit();
    echo "Payment processed successfully. Receipt and QR code sent to $email.";
}

// Example of processing a payment
try {
    processPayment(1, 'Cash', 1000, 1500);  // Example member ID, payment type, amount, and amount paid
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
