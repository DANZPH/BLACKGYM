<?php
session_start();
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $changeAmount = $amountPaid - $amount;

    if ($amountPaid < $amount) {
        echo "Error: Amount paid cannot be less than the amount.";
        exit();
    }

    $conn1->begin_transaction();

    try {
        // Insert payment details
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Update membership status
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);

        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating membership status in Members: " . $updateMemberStmt->error);
        }

        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("d", $memberID);

        if (!$updateMembershipStmt->execute()) {
            throw new Exception("Error updating membership status in Membership: " . $updateMembershipStmt->error);
        }

        // Calculate end date
        $numMonths = floor($amount / 600);
        $endDate = date('Y-m-d', strtotime("+$numMonths months"));

        $updateEndDateStmt = $conn1->prepare("UPDATE Membership SET EndDate = ? WHERE MemberID = ?");
        $updateEndDateStmt->bind_param("sd", $endDate, $memberID);

        if (!$updateEndDateStmt->execute()) {
            throw new Exception("Error updating EndDate in Membership: " . $updateEndDateStmt->error);
        }

        // Fetch member details
        $memberStmt = $conn1->prepare("SELECT * FROM Members WHERE MemberID = ?");
        $memberStmt->bind_param("d", $memberID);
        $memberStmt->execute();
        $memberResult = $memberStmt->get_result();
        $member = $memberResult->fetch_assoc();

        if (!$member) {
            throw new Exception("Member not found.");
        }

        // Receipt Details
        $receiptNumber = uniqid('REC-', true);
        $transactionDate = date('Y-m-d H:i:s');
        $customerName = $member['FullName'];
        $customerEmail = $member['Email'];
        $billingAddress = $member['Address'];

        $receiptContent = "
        <html>
        <head>
            <style>
                .receipt { font-family: Arial, sans-serif; }
                .receipt h1 { text-align: center; }
                .receipt table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .receipt th, .receipt td { padding: 8px 12px; border: 1px solid #ddd; }
                .receipt .footer { margin-top: 20px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='receipt'>
                <h1>Business Name</h1>
                <p>Business Address | Phone: 123-456-7890 | Email: info@business.com</p>
                <h2>RECEIPT</h2>
                <p>Receipt Number: $receiptNumber</p>
                <p>Date: $transactionDate</p>
                <p>Time: " . date('H:i:s') . "</p>

                <h3>Customer Information:</h3>
                <p>Name: $customerName</p>
                <p>Email: $customerEmail</p>
                <p>Billing Address: $billingAddress</p>

                <h3>Transaction Details:</h3>
                <table>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td>Membership Fee</td>
                        <td>1</td>
                        <td>\$$amount</td>
                        <td>\$$amount</td>
                    </tr>
                </table>

                <p><strong>Subtotal:</strong> \$$amount</p>
                <p><strong>Amount Paid:</strong> \$$amountPaid</p>
                <p><strong>Change Given:</strong> \$$changeAmount</p>
                <p><strong>End Date:</strong> $endDate</p>

                <h3>Payment Method: $paymentType</h3>
                <h3>Transaction ID: $receiptNumber</h3>

                <div class='footer'>
                    <p>Thank you for your payment!</p>
                    <p>Visit our website for more information.</p>
                </div>
            </div>
        </body>
        </html>";

        // Send the receipt
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mail.blackgym@gmail.com';
            $mail->Password = 'akbbhmrrxzryovqt';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('mail.blackgym@gmail.com');
            $mail->addAddress($customerEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Payment Receipt';
            $mail->Body = $receiptContent;

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error sending email: " . $mail->ErrorInfo);
        }

        $conn1->commit();
        echo "Payment processed, receipt sent to $customerEmail!";
    } catch (Exception $e) {
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    $stmt->close();
    $updateMemberStmt->close();
    $updateMembershipStmt->close();
    $updateEndDateStmt->close();
}

$conn1->close();
?>