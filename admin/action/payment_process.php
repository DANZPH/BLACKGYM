<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; 
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

// Manually include Dompdf
require_once '../../dompdf/autoload.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

function sendReceiptEmail($email, $name, $pdfContent) {
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender and recipient
        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
        $mail->addAddress($email);

        // Set the email body content (even if it's just simple)
        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful';
        $mail->Body    = "<h2>Dear $name,</h2>
                          <p>Your payment has been successfully processed. Please find your receipt attached.</p>";

        // Attach PDF to the email
        $mail->addStringAttachment($pdfContent, 'receipt.pdf', 'base64', 'application/pdf');

        // Enable debug output to troubleshoot email sending
        $mail->SMTPDebug = 0;  // 0 = off, 1 = client, 2 = client and server
        $mail->Debugoutput = 'html';  // Show debug info in HTML format

        // Attempt to send the email
        $mail->send();
    } catch (Exception $e) {
        /*error_log("Mail Error: " . $e->getMessage());
        echo "Error sending email: " . $e->getMessage();*/
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $changeAmount = $amountPaid - $amount;
    $receiptNumber = uniqid('RCT-');
    $paymentDate = date('Y-m-d H:i:s');

    if ($amountPaid < $amount) {
        echo "Error: Amount paid cannot be less than the amount.";
        exit();
    }

    $conn1->begin_transaction();

    try {
        // Insert payment details into the database
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

        // Update membership details with new end date
        $subscription = $amount;
        $numMonths = floor($subscription / 600);
        $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));

        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("sd", $endDate, $memberID);
        if (!$updateMembershipStmt->execute()) {
            throw new Exception("Error updating membership: " . $updateMembershipStmt->error);
        }

        // Fetch the email and name of the member
        $emailQuery = $conn1->prepare("SELECT Users.Email, Users.Username AS Name FROM Users 
                                       INNER JOIN Members ON Users.UserID = Members.UserID 
                                       WHERE Members.MemberID = ?");
        $emailQuery->bind_param("d", $memberID);
        $emailQuery->execute();
        $emailQuery->bind_result($email, $name);
        $emailQuery->fetch();
        $emailQuery->close();

        // Build HTML content for the receipt
        $receiptHtml = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        .container { width: 100%; max-width: 800px; margin: auto; }
                        .header { text-align: center; }
                        .header h1 { font-size: 32px; margin: 0; }
                        .header p { margin: 5px 0; font-size: 16px; }
                        .receipt-info { margin-top: 30px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
                        .receipt-info h3 { margin-bottom: 10px; }
                        .receipt-info table { width: 100%; margin-top: 10px; border-collapse: collapse; }
                        .receipt-info table, .receipt-info th, .receipt-info td { border: 1px solid #ddd; padding: 10px; }
                        .footer { text-align: center; margin-top: 20px; font-size: 12px; }
                        .footer a { text-decoration: none; color: #007bff; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>BLACK GYM PAYMENT RECEIPT</h1>
                            <p>Gym Name: Black Gym</p>
                            <p>Address: 123 Matina, Davao City</p>
                            <p>Contact: +63 9123 456 7890 | Email: mail@blackgym.com</p>
                        </div>
                        <div class='receipt-info'>
                            <h3>Receipt Details</h3>
                            <p><strong>Receipt Number:</strong> $receiptNumber</p>
                            <p><strong>Payment Date:</strong> $paymentDate</p>
                            <p><strong>Name:</strong> $name</p>
                            <p><strong>Email:</strong> $email</p>
                            <p><strong>Membership Up To:</strong> $endDate</p>
                            <h4>Payment Details</h4>
                            <table>
                                <tr><th>Amount Due</th><td>Php: " . number_format($amount, 2) . "</td></tr>
                                <tr><th>Amount Paid</th><td>Php: " . number_format($amountPaid, 2) . "</td></tr>
                                <tr><th>Change</th><td>Php: " . number_format($changeAmount, 2) . "</td></tr>
                            </table>
                        </div>
                        <div class='footer'>
                            <p>Thank you for your payment!</p>
                            <p>Visit <a href='https://gym.dazx.xyz'>gym.dazx.xyz</a> for more information.</p>
                        </div>
                    </div>
                </body>
            </html>
        ";

        // Convert the HTML to PDF using Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($receiptHtml);
        $dompdf->render();
        $pdfContent = $dompdf->output();

        // Send the receipt email with the attached PDF
        sendReceiptEmail($email, $name, $pdfContent);

        $conn1->commit();

        echo "Payment processed successfully. Receipt sent to $email.";
    } catch (Exception $e) {
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    $stmt->close();
    $updateMemberStmt->close();
    $updateMembershipStmt->close();
}
$conn1->close();
?>
