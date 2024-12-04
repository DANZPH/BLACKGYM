<?php
include '../../database/connection.php'; 
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

        // HTML content for the receipt
        $mail->Body = "
            <html>
                <head>
                    <style>
                        .receipt-container {
                            max-width: 600px;
                            margin: 20px auto;
                            border: 1px solid #ddd;
                            padding: 20px;
                            border-radius: 10px;
                            background-color: #f9f9f9;
                        }
                        .receipt-header {
                            text-align: center;
                        }
                        .receipt-footer {
                            text-align: center;
                            font-size: 12px;
                            margin-top: 20px;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <div class='receipt-container'>
                        <div class='receipt-header'>
                            <h2>BLACK GYM PAYMENT RECEIPT</h2>
                            <p>Gym Name: Black Gym</p>
                            <p>Address: 123 Matina, Davao City</p>
                            <p>Contact: +63 9123 456 7890 | Email: mail@blackgym.com</p>
                            <hr>
                        </div>
                        <div class='receipt-body'>
                            <p><strong>Member ID:</strong> $memberID</p>
                            <p><strong>Payment Type:</strong> $paymentType</p>
                            <p><strong>Amount Due:</strong> PHP " . number_format($amount, 2) . "</p>
                            <p><strong>Amount Paid:</strong> PHP " . number_format($amountPaid, 2) . "</p>
                            <p><strong>Change:</strong> PHP " . number_format($change, 2) . "</p>
                        </div>
                        <div class='receipt-footer'>
                            <p>Thank you for being a member of Black Gym!</p>
                            <p>Page rendered on " . date('Y-m-d H:i:s') . "</p>
                        </div>
                    </div>
                </body>
            </html>
        ";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}
?>
