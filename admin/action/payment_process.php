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
    $chanceAmount = $_POST['chanceAmount']; // Added ChanceAmount
    $addChanceToBalance = $_POST['addToBalance']; // YES, NO, WITHDRAW
    $receiptNumber = uniqid('RCT-');
    $paymentDate = date('Y-m-d H:i:s');

    // No longer check if the amount paid is less than the amount
    // Remove the error handling for 'Amount Paid < Total Bill' to allow for negative balance

    // Adjust AmountPaid if ChanceAmount should be added to the balance
    if ($addChanceToBalance == "yes") {
        $amountPaid += $chanceAmount; // Add ChanceAmount to AmountPaid if the checkbox is checked
    }
    
    $changeAmount = $amountPaid - $amount;

    $conn1->begin_transaction();

    try {
        // Insert payment details into the database
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount, ReceiptNumber, PaymentDate) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddsss", $memberID, $paymentType, $amount, $amountPaid, $changeAmount, $receiptNumber, $paymentDate);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Fetch the current balance of the member
        $currentBalanceQuery = $conn1->prepare("SELECT Balance FROM Members WHERE MemberID = ?");
        $currentBalanceQuery->bind_param("d", $memberID);
        $currentBalanceQuery->execute();
        $currentBalanceQuery->bind_result($currentBalance);
        $currentBalanceQuery->fetch();
        $currentBalanceQuery->close();

        // Handle different addChanceToBalance options
        if ($addChanceToBalance === 'withdraw') {
            // If 'WITHDRAW', set the balance to 0
            $newBalance = 0;
        } elseif ($addChanceToBalance === 'no') {
            // If 'NO', do nothing (keep the current balance)
            $newBalance = $currentBalance;
        } elseif ($addChanceToBalance === 'yes') {
            // If 'YES', add the change amount to the balance
            $newBalance = $currentBalance + $changeAmount;
        }

        // Update the balance of the member
        $updateBalanceStmt = $conn1->prepare("UPDATE Members SET Balance = ? WHERE MemberID = ?");
        $updateBalanceStmt->bind_param("di", $newBalance, $memberID); // Assuming Balance is a decimal type
        if (!$updateBalanceStmt->execute()) {
            throw new Exception("Error updating balance: " . $updateBalanceStmt->error);
        }

        // Update membership status
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);
        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating membership status: " . $updateMemberStmt->error);
        }

        // Calculate the total subscription price by combining subscription and session price
        $sessionPrice = $_POST['sessionPrice']; // Assuming you get session price from POST or some other method
        $totalAmount = $amount + $sessionPrice;

        // Divide by 600 to get full months
        $numMonths = floor($totalAmount / 600);

        // Calculate remaining balance after division by 600 (use this for days)
        $remainingAmount = $totalAmount % 600;
        $extraDays = floor($remainingAmount / 50); // Each 50 pesos equals 1 extra day

        // Fetch the current EndDate for the membership
        $currentEndDateQuery = $conn1->prepare("SELECT EndDate FROM Membership WHERE MemberID = ?");
        $currentEndDateQuery->bind_param("d", $memberID);
        $currentEndDateQuery->execute();
        $currentEndDateQuery->bind_result($currentEndDate);
        $currentEndDateQuery->fetch();
        $currentEndDateQuery->close();

        // Determine the new end date
        if ($currentEndDate && strtotime($currentEndDate) > time()) {
            // If the current end date is in the future, add the subscription duration and extra days to it
            $newEndDate = date('Y-m-d H:i:s', strtotime("$currentEndDate +$numMonths months +$extraDays days"));
        } else {
            // If the current end date is in the past or not set, start from now
            $newEndDate = date('Y-m-d H:i:s', strtotime("+$numMonths months +$extraDays days"));
        }

        // Update membership details with the new end date
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("sd", $newEndDate, $memberID);
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
                            <h1>Black Gym</h1>
                            <p>Receipt for Payment</p>
                        </div>
                        <div class='receipt-info'>
                            <h3>Payment Information</h3>
                            <table>
                                <tr>
                                    <th>Receipt Number</th>
                                    <td>$receiptNumber</td>
                                </tr>
                                <tr>
                                    <th>Member Name</th>
                                    <td>$name</td>
                                </tr>
                                <tr>
                                    <th>Payment Type</th>
                                    <td>$paymentType</td>
                                </tr>
                                <tr>
                                    <th>Total Bill</th>
                                    <td>$amount</td>
                                </tr>
                                <tr>
                                    <th>Amount Paid</th>
                                    <td>$amountPaid</td>
                                </tr>
                                <tr>
                                    <th>Change</th>
                                    <td>$changeAmount</td>
                                </tr>
                                <tr>
                                    <th>Balance</th>
                                    <td>$newBalance</td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>$paymentDate</td>
                                </tr>
                            </table>
                        </div>
                        <div class='footer'>
                            <p>Thank you for being a valued member of Black Gym!</p>
                            <p><a href='#'>Download this receipt</a></p>
                        </div>
                        <div class='qr-container'>
                            <img src='data:image/png;base64,$qrCodeBase64' alt='QR Code'/>
                        </div>
                    </div>
                </body>
            </html>
        ";

        // Convert the receipt HTML to PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($receiptHtml);
        $dompdf->setOptions(new Options(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]));
        $dompdf->render();
        $pdfContent = $dompdf->output();

        // Send email with the receipt
        sendReceiptEmail($email, $name, $pdfContent);

        // Commit transaction
        $conn1->commit();
        echo "Payment successfully processed and receipt emailed!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }
}
?>
