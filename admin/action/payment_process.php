1
<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}
require_once '../../vendor/autoload.php';
include '../../database/connection.php'; // Database connection
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';
require '../../fpdf/fpdf.php'; // Include the FPDF library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'BLACK GYM PAYMENT RECEIPT', 0, 1, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, 'Gym Name: Black Gym', 0, 1, 'C');
        $this->Cell(0, 6, 'Address: 123 Matina, Davao City', 0, 1, 'C');
        $this->Cell(0, 6, 'Contact: +63 9123 456 7890 | Email: mail@blackgym.com', 0, 1, 'C');
        $this->Ln(6);

        // Add a line after the header
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
        
        // Add a compact table for payment breakdown
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
        
        // Add a line after the payment details
        $this->Ln(6);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);
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
        $mail->Subject = 'Payment Successfull';
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
        
        // Receipt details
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, "Receipt Number: $receiptNumber", 0, 1);
        $pdf->Cell(0, 6, "Payment Date: $paymentDate", 0, 1);
        $pdf->Cell(0, 6, "Name: $name", 0, 1);
        $pdf->Cell(0, 6, "Membership Up To: $endDate", 0, 1);
        $pdf->Cell(0, 6, "Email: $email", 0, 1);  // Added member email
        $pdf->Ln(4);

        // Add payment details table
        $pdf->PaymentDetailsTable([
            'receiptNumber' => $receiptNumber,
            'paymentDate' => $paymentDate,
            'amount' => $amount,
            'amountPaid' => $amountPaid,
            'changeAmount' => $changeAmount
        ]);

        // Footer
        $pdf->Cell(0, 6, "Thank you for your payment!", 0, 1, 'C');

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