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
require '../../fpdf/fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class PDF extends FPDF {
    // Header and Footer as before
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'BLACK GYM PAYMENT RECEIPT', 0, 1, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, 'Gym Name: Black Gym', 0, 1, 'C');
        $this->Cell(0, 6, 'Address: 123 Matina, Davao City', 0, 1, 'C');
        $this->Cell(0, 6, 'Contact: +63 9123 456 7890 | Email: mail@blackgym.com', 0, 1, 'C');
        $this->Ln(6);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 4, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function PaymentDetailsTable($paymentData) {
        $this->SetFont('Arial', '', 9);
        $this->Cell(40, 6, 'Receipt Number:', 0, 0);
        $this->Cell(40, 6, $paymentData['receiptNumber'], 0, 1);
        $this->Cell(40, 6, 'Payment Date:', 0, 0);
        $this->Cell(40, 6, $paymentData['paymentDate'], 0, 1);
        $this->Cell(40, 6, 'Amount Due:', 0, 0);
        $this->Cell(40, 6, 'P' . number_format($paymentData['amount'], 2), 0, 1);
        $this->Cell(40, 6, 'Amount Paid:', 0, 0);
        $this->Cell(40, 6, 'P' . number_format($paymentData['amountPaid'], 2), 0, 1);
        $this->Cell(40, 6, 'Change:', 0, 0);
        $this->Cell(40, 6, 'P' . number_format($paymentData['changeAmount'], 2), 0, 1);
        $this->Ln(6);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);
    }

    function QRCode($qrCodeImageData) {
        try {
            // Embed QR code image directly into PDF using raw image data
            $this->Image('@' . $qrCodeImageData, 150, 50, 30, 30);  // Adjust position and size as needed
        } catch (Exception $e) {
            echo 'Error embedding QR code: ' . $e->getMessage();
        }
    }
}

function sendReceiptEmail($email, $name, $pdfContent) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt'; // Update this with your SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful';
        $mail->Body = "<p>Dear $name,</p><p>Please find your payment receipt attached.</p><p>Thank you for being a member of Black Gym!</p>";

        $mail->addStringAttachment($pdfContent, 'PaymentReceipt.pdf');
        $mail->send();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

function processPayment($memberID, $paymentType, $amount, $amountPaid) {
    global $conn1;

    $changeAmount = $amountPaid - $amount;
    $receiptNumber = uniqid('RCT-');
    $paymentDate = date('Y-m-d H:i:s');

    if ($amountPaid < $amount) {
        throw new Exception("Amount paid cannot be less than the amount.");
    }

    $conn1->begin_transaction();

    // Insert payment into the database
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

    // Update membership end date
    $subscription = $amount;
    $numMonths = floor($subscription / 600);
    $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));

    $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?");
    $updateMembershipStmt->bind_param("sd", $endDate, $memberID);
    if (!$updateMembershipStmt->execute()) {
        throw new Exception("Error updating membership: " . $updateMembershipStmt->error);
    }

    // Fetch email and name for the member
    $emailQuery = $conn1->prepare("SELECT Users.Email, Users.Username AS Name FROM Users 
                                   INNER JOIN Members ON Users.UserID = Members.UserID 
                                   WHERE Members.MemberID = ?");
    $emailQuery->bind_param("d", $memberID);
    $emailQuery->execute();
    $emailQuery->bind_result($email, $name);
    $emailQuery->fetch();
    $emailQuery->close();

    // Generate QR code content
    $builder = new Builder(
        writer: new PngWriter(),
        data: $receiptNumber,  // Use receiptNumber as content for the QR code
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10
    );

    $qrCodeResult = $builder->build();
    $qrCodeImageData = $qrCodeResult->getString();  // Get the QR code image as raw data

    // Generate PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Add receipt details
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 6, "Receipt Number: $receiptNumber", 0, 1);
    $pdf->Cell(0, 6, "Payment Date: $paymentDate", 0, 1);
    $pdf->Cell(0, 6, "Name: $name", 0, 1);
    $pdf->Cell(0, 6, "Membership Up To: $endDate", 0, 1);
    $pdf->Cell(0, 6, "Email: $email", 0, 1);

    // Add payment details table
    $pdf->PaymentDetailsTable([
        'receiptNumber' => $receiptNumber,
        'paymentDate' => $paymentDate,
        'amount' => $amount,
        'amountPaid' => $amountPaid,
        'changeAmount' => $changeAmount
    ]);

    // Embed QR code into the PDF
    $pdf->QRCode($qrCodeImageData);

    // Output PDF as a string (for email attachment)
    $pdfContent = $pdf->Output('S');

    // Send email with PDF attached
    sendReceiptEmail($email, $name, $pdfContent);

    // Commit the transaction
    $conn1->commit();
    echo "Payment processed successfully. Receipt sent to $email.";
}

// Example of processing a payment
try {
    processPayment(1, 'Cash', 1000, 1500);  // Example member ID, payment type, amount, and amount paid
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
