<?php
session_start();
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $email = $_POST['email'];

    $change = $amountPaid - $amount;

    if ($amountPaid >= $amount) {
        // Update membership status
        $endDate = date('Y-m-d', strtotime('+1 month'));
        $sql = "UPDATE Membership SET Status = 'Active', EndDate = ? WHERE MemberID = ?";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param('si', $endDate, $memberID);

        if ($stmt->execute()) {
            // Log payment
            $paymentLogSQL = "INSERT INTO Payments (MemberID, AmountPaid, PaymentType, PaymentDate) VALUES (?, ?, ?, NOW())";
            $paymentLogStmt = $conn1->prepare($paymentLogSQL);
            $paymentLogStmt->bind_param('ids', $memberID, $amountPaid, $paymentType);
            $paymentLogStmt->execute();

            // Call the receipt email function
            sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType);

            echo 'Payment successful and receipt sent.';
        } else {
            echo 'Error updating membership.';
        }
    } else {
        echo 'Amount paid is less than the required amount.';
    }
}
$conn1->close();

/**
 * Function to send payment receipt via email.
 */
function sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType) {
    require '../../login/phpmailer/src/Exception.php';
    require '../../login/phpmailer/src/PHPMailer.php';
    require '../../login/phpmailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Replace with your email
        $mail->Password = 'your_app_password'; // Replace with your app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('your_email@gmail.com', 'Your Business'); // Replace with your sender email
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
        error_log("Email Error: " . $e->getMessage());
    }
}
?>