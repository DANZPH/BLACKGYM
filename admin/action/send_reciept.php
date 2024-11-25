<?php
session_start();
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $changeAmount = $amountPaid - $amount;

    // Fetch Member details from the database
    $stmt = $conn1->prepare("SELECT * FROM Members WHERE MemberID = ?");
    $stmt->bind_param("d", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    // Receipt Details
    $receiptNumber = uniqid('REC-', true);
    $transactionDate = date('Y-m-d H:i:s');
    $customerName = $member['FullName'];
    $customerEmail = $member['Email'];
    $billingAddress = $member['Address'];

    // Calculate number of months and the EndDate
    $numMonths = floor($amount / 600);
    $endDate = date('Y-m-d', strtotime("+$numMonths months"));

    // Create the receipt content
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

    // Send the receipt via email
    function sendReceipt($email, $receiptContent) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mail.blackgym@gmail.com'; // Your Gmail
            $mail->Password = 'akbbhmrrxzryovqt'; // Your Gmail app password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('mail.blackgym@gmail.com'); // Your Gmail
            $mail->addAddress($email); // Recipient's email

            $mail->isHTML(true);
            $mail->Subject = 'Payment Receipt';
            $mail->Body = $receiptContent;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }

    // Call the function to send the receipt
    if (sendReceipt($customerEmail, $receiptContent)) {
        echo "Receipt has been sent to $customerEmail!";
    } else {
        echo "There was an error sending the receipt.";
    }
}

$conn1->close();
?>