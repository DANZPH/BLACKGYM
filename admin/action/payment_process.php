<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

require_once '../../vendor/autoload.php';
include '../../database/connection.php'; 
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReceiptEmail($email, $name, $receiptHtml) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful';
        $mail->Body = $receiptHtml;

        $mail->send();
    } catch (Exception $e) {
        error_log($e->getMessage());
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
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount, ReceiptNumber, PaymentDate) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddsss", $memberID, $paymentType, $amount, $amountPaid, $changeAmount, $receiptNumber, $paymentDate);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);
        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating membership status: " . $updateMemberStmt->error);
        }

        $subscription = $amount;
        $numMonths = floor($subscription / 600);
        $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));

        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("sd", $endDate, $memberID);
        if (!$updateMembershipStmt->execute()) {
            throw new Exception("Error updating membership: " . $updateMembershipStmt->error);
        }

        // Fetching email and name for the member
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
                        .download-pdf-btn { display: block; text-align: center; margin: 20px 0; }
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
                                <tr><th>Amount Due</th><td>P" . number_format($amount, 2) . "</td></tr>
                                <tr><th>Amount Paid</th><td>P" . number_format($amountPaid, 2) . "</td></tr>
                                <tr><th>Change</th><td>P" . number_format($changeAmount, 2) . "</td></tr>
                            </table>
                        </div>
                        <div class='footer'>
                            <p>Thank you for your payment!</p>
                            <p>Visit <a href='https://www.blackgym.com'>www.blackgym.com</a> for more information.</p>
                        </div>
                        <div class='download-pdf-btn'>
                            <button onclick='downloadReceipt()'>Download PDF</button>
                        </div>
                    </div>
                </body>
            </html>

            <script src='https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js'></script>
            <script>
                function downloadReceipt() {
                    const element = document.querySelector('.container');
                    const options = {
                        margin: 10,
                        filename: 'receipt_$receiptNumber.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2 },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    };
                    html2pdf().from(element).set(options).save();
                }
            </script>
        ";

        // Send the receipt email with HTML content
        sendReceiptEmail($email, $name, $receiptHtml);

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
