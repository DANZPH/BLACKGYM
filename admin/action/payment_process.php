<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com'; // Your email
        $mail->Password = 'akbbhmrrxzryovqt';       // App password
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
        error_log($e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $changeAmount = $amountPaid - $amount; // Calculate the change (if any)

    // Check if the amount paid is sufficient
    if ($amountPaid < $amount) {
        echo "Error: Amount paid cannot be less than the amount.";
        exit();
    }

    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Update the Members table
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);
        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating membership status in Members: " . $updateMemberStmt->error);
        }

        // Update the Membership table
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("d", $memberID);
        if (!$updateMembershipStmt->execute()) {
            throw new Exception("Error updating membership status in Membership: " . $updateMembershipStmt->error);
        }

        // Calculate EndDate
        $subscription = $amount;
        $numMonths = floor($subscription / 600);
        $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));
        $updateEndDateStmt = $conn1->prepare("UPDATE Membership SET EndDate = ? WHERE MemberID = ?");
        $updateEndDateStmt->bind_param("sd", $endDate, $memberID);
        if (!$updateEndDateStmt->execute()) {
            throw new Exception("Error updating EndDate in Membership: " . $updateEndDateStmt->error);
        }

        // Fetch email of the member
        $emailQuery = $conn1->prepare("SELECT Email FROM Users INNER JOIN Members ON Users.UserID = Members.UserID WHERE Members.MemberID = ?");
        $emailQuery->bind_param("d", $memberID);
        $emailQuery->execute();
        $emailQuery->bind_result($email);
        $emailQuery->fetch();
        $emailQuery->close();

        // Send the email receipt
        sendReceipt($email, $memberID, $amount, $amountPaid, $changeAmount, $paymentType);

        // Commit the transaction
        $conn1->commit();

        echo "Payment processed, statuses updated to Active, and EndDate set. Receipt sent to $email.";
    } catch (Exception $e) {
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Close statements
    $stmt->close();
    $updateMemberStmt->close();
    $updateMembershipStmt->close();
    $updateEndDateStmt->close();
}

$conn1->close();
?>