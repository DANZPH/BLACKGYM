<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Database connection
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com'; // Replace with your email
        $mail->Password = 'akbbhmrrxzryovqt'; // Replace with your app-specific password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Your Business'); // Replace with your email
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Receipt';
        $mail->Body = "
            <h1>Payment Receipt</h1>
            <p>Member ID: $memberID</p>
            <p>Payment Type: $paymentType</p>
            <p>Amount Due: $$amount</p>
            <p>Amount Paid: $$amountPaid</p>
            <p>Change: $$change</p>
            <p>Thank you for your payment!</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = intval($_POST['memberID']);
    $amount = 100.00; // Example payment amount
    $amountPaid = 100.00; // Assume full payment
    $paymentMethod = 'Cash'; // Example payment method
    $change = $amountPaid - $amount;

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert payment record
        $sql_payment = "INSERT INTO Payments (MemberID, Amount, PaymentMethod) VALUES (?, ?, ?)";
        $stmt_payment = $conn->prepare($sql_payment);
        $stmt_payment->bind_param("ids", $memberID, $amount, $paymentMethod);
        $stmt_payment->execute();

        // Update membership status to 'Active'
        $sql_membership = "UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?";
        $stmt_membership = $conn->prepare($sql_membership);
        $stmt_membership->bind_param("i", $memberID);
        $stmt_membership->execute();

        // Get member email
        $sql_email = "SELECT Email FROM Users INNER JOIN Members ON Users.UserID = Members.UserID WHERE Members.MemberID = ?";
        $stmt_email = $conn->prepare($sql_email);
        $stmt_email->bind_param("i", $memberID);
        $stmt_email->execute();
        $result = $stmt_email->get_result();
        $email = $result->fetch_assoc()['Email'];

        // Commit the transaction
        $conn->commit();

        // Send receipt via email
        sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentMethod);

        echo "Payment processed successfully for Member ID: $memberID and membership status updated to Active. Receipt has been emailed.";
    } catch (Exception $e) {
        // Rollback the transaction if anything goes wrong
        $conn->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Clean up
    $stmt_payment->close();
    $stmt_membership->close();
    $stmt_email->close();
    $conn->close();
}
?>