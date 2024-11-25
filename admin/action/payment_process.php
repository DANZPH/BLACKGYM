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
use PHPMailer\PHPMailer\Exception;

// Function to send receipt email
function sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt'; // Replace with a secure app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Your Business');
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
        error_log('Mailer Error: ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = intval($_POST['memberID']);
    $amount = 100.00; // Example payment amount
    $amountPaid = 100.00; // Example full payment
    $change = $amountPaid - $amount; // Calculate change
    $paymentType = 'Cash'; // Example payment method

    // Fetch member's email
    $sql_email = "SELECT Users.Email FROM Members 
                  INNER JOIN Users ON Members.UserID = Users.UserID 
                  WHERE Members.MemberID = ?";
    $stmt_email = $conn->prepare($sql_email);
    $stmt_email->bind_param("i", $memberID);
    $stmt_email->execute();
    $result = $stmt_email->get_result();
    $email = $result->fetch_assoc()['Email'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert payment record
        $sql_payment = "INSERT INTO Payments (MemberID, Amount, PaymentMethod) VALUES (?, ?, ?)";
        $stmt_payment = $conn->prepare($sql_payment);
        $stmt_payment->bind_param("ids", $memberID, $amount, $paymentType);
        $stmt_payment->execute();

        // Update membership status to 'Active'
        $sql_membership = "UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?";
        $stmt_membership = $conn->prepare($sql_membership);
        $stmt_membership->bind_param("i", $memberID);
        $stmt_membership->execute();

        // Commit the transaction
        $conn->commit();

        // Send email receipt
        sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType);

        echo "Payment processed successfully for Member ID: $memberID. An email receipt has been sent.";
    } catch (Exception $e) {
        // Rollback the transaction if anything goes wrong
        $conn->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Clean up
    $stmt_email->close();
    $stmt_payment->close();
    $stmt_membership->close();
    $conn->close();
}
?>