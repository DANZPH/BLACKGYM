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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
    <div class="receipt-container" id="receipt">
        <div class="receipt-header">
            <h2>BLACK GYM PAYMENT RECEIPT</h2>
            <p>Gym Name: Black Gym</p>
            <p>Address: 123 Matina, Davao City</p>
            <p>Contact: +63 9123 456 7890 | Email: mail@blackgym.com</p>
            <hr>
        </div>
        <div class="receipt-body">
            <p><strong>Receipt Number:</strong> <?php echo $receiptNumber; ?></p>
            <p><strong>Payment Date:</strong> <?php echo $paymentDate; ?></p>
            <p><strong>Name:</strong> <?php echo $name; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Membership Up To:</strong> <?php echo $endDate; ?></p>
            <hr>
            <h5>Payment Details</h5>
            <p><strong>Amount Due:</strong> PHP <?php echo number_format($amount, 2); ?></p>
            <p><strong>Amount Paid:</strong> PHP <?php echo number_format($amountPaid, 2); ?></p>
            <p><strong>Change:</strong> PHP <?php echo number_format($changeAmount, 2); ?></p>
        </div>
        <div class="receipt-footer">
            <p>Thank you for being a member of Black Gym!</p>
            <p>Page rendered on <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
    <div class="text-center mt-3">
        <button class="btn btn-primary" id="download">Download PDF</button>
    </div>
    <script>
        document.getElementById('download').addEventListener('click', () => {
            const element = document.getElementById('receipt');
            html2pdf()
                .from(element)
                .set({
                    margin: 10,
                    filename: 'PaymentReceipt.pdf',
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                })
                .save();
        });
    </script>
</body>
</html>
