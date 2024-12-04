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

        // Attempt to send the email
        $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $e->getMessage());
        echo "Error sending email: " . $e->getMessage();
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

        // Generate the QR Code as Base64 Image
        $qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?data=$receiptNumber&size=150x150";
        $qrCodeBase64 = base64_encode(file_get_contents($qrCodeURL)); // Convert to base64

        // Build HTML content for the receipt
        $receiptHtml = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                        .container { width: 100%; max-width: 800px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .header { text-align: center; margin-bottom: 30px; }
                        .header h1 { font-size: 36px; margin: 0; color: #333; }
                        .header p { margin: 5px 0; font-size: 16px; color: #666; }
                        .receipt-info { margin-top: 30px; padding: 20px; background-color: #fafafa; border-radius: 8px; border: 1px solid #ddd; }
                        .receipt-info h3 { margin-bottom: 10px; font-size: 24px; color: #333; }
                        .receipt-info table { width: 100%; margin-top: 10px; border-collapse: collapse; }
                        .receipt-info table, .receipt-info th, .receipt-info td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        .receipt-info th { background-color: #f1f1f1; }
                        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #777; }
                        .footer a { text-decoration: none; color: #007bff; }
                        .qr-container { text-align: center; margin-bottom: 20px; }
                        .qr-container img { width: 150px; height: 150px; }
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
                        <div class='qr-container'>
                            <img src='data:image/png;base64,$qrCodeBase64' alt='QR Code' />
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
                                <tr><th>Amount Due</th><td>P" . number_format($amount, 2) . "</td></tr>
                                <tr><th>Amount Paid</th><td>P" . number_format($amountPaid, 2) . "</td></tr>
                                <tr><th>Change</th><td>P" . number_format($changeAmount, 2) . "</td></tr>
                            </table>
                        </div>
                        <div class='footer'>
                            <p>Thank you for your payment!</p>
                            <p>Visit <a href='https://www.blackgym.com'>www.blackgym.com</a> for more information.</p>
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

        // Send the receipt to the user by email
        sendReceiptEmail($email, $name, $pdfContent);

        // Commit the transaction
        $conn1->commit();
        echo "Payment processed and receipt sent successfully!";
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn1->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
