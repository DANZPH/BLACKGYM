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
require '../../fpdf/fpdf.php'; // Include the FPDF library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PDF extends FPDF {
    function Header() {
        // Set a larger font size for title
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, 'BLACK GYM PAYMENT RECEIPT', 0, 1, 'C');
        $this->Ln(10);

        // Add a gym logo (if available)
        // $this->Image('path/to/logo.png', 10, 10, 30); // Uncomment if you have a logo

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Gym Name: Black Gym', 0, 1, 'C');
        $this->Cell(0, 10, 'Address: 123 Fitness St, Healthy City', 0, 1, 'C');
        $this->Cell(0, 10, 'Contact: +1 (234) 567-890', 0, 1, 'C');
        $this->Cell(0, 10, 'Email: contact@blackgym.com', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        $this->Ln(5);
        // Add extra footer information like "Thank you for being a member!"
        $this->Cell(0, 10, 'Thank you for being a member of Black Gym!', 0, 0, 'C');
    }

    function PaymentDetailsTable($paymentData) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(100, 10, 'Payment Details:', 0, 1);
        $this->SetFont('Arial', '', 12);
        
        // Add a table for payment breakdown
        $this->Cell(50, 10, 'Receipt Number:', 1, 0);
        $this->Cell(50, 10, $paymentData['receiptNumber'], 1, 1);
        
        $this->Cell(50, 10, 'Payment Date:', 1, 0);
        $this->Cell(50, 10, $paymentData['paymentDate'], 1, 1);
        
        $this->Cell(50, 10, 'Amount Due:', 1, 0);
        $this->Cell(50, 10, '$' . number_format($paymentData['amount'], 2), 1, 1);
        
        $this->Cell(50, 10, 'Amount Paid:', 1, 0);
        $this->Cell(50, 10, '$' . number_format($paymentData['amountPaid'], 2), 1, 1);
        
        $this->Cell(50, 10, 'Change:', 1, 0);
        $this->Cell(50, 10, '$' . number_format($paymentData['changeAmount'], 2), 1, 1);
        
        $this->Ln(5);
    }
}

function sendReceiptEmail($email, $name, $pdfContent) {
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
        $mail->Subject = 'Payment Receipt';
        $mail->Body = "<p>Dear $name,</p><p>Please find your payment receipt attached.</p><p>Thank you for being a member of Black Gym!</p>";

        $mail->addStringAttachment($pdfContent, 'PaymentReceipt.pdf');
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

        // Generate PDF
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Receipt details
        $pdf->Cell(0, 10, "Receipt Number: $receiptNumber", 0, 1);
        $pdf->Cell(0, 10, "Payment Date: $paymentDate", 0, 1);
        $pdf->Cell(0, 10, "Name: $name", 0, 1);
        $pdf->Cell(0, 10, "Membership Up To: $endDate", 0, 1);
        $pdf->Cell(0, 10, "Email: $email", 0, 1);  // Added member email
        $pdf->Ln(10);

        // Add payment details table
        $pdf->PaymentDetailsTable([
            'receiptNumber' => $receiptNumber,
            'paymentDate' => $paymentDate,
            'amount' => $amount,
            'amountPaid' => $amountPaid,
            'changeAmount' => $changeAmount
        ]);

        // Footer
        $pdf->Cell(0, 10, "Thank you for your payment!", 0, 1, 'C');

        // Output PDF content as string for emailing
        $pdfContent = $pdf->Output('S');

        // Send receipt email
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