<?php
require '../../login/phpmailer/src/Exception.php';
require '../../login/phpmailer/src/PHPMailer.php';
require '../../login/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendReceipt($email, $memberID, $amount, $amountPaid, $change, $paymentType) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com', 'Your Business');
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
        error_log($e->getMessage());
    }
}
?>