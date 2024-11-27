function sendReceiptEmail($email, $name, $pdfContent, $receiptNumber) {
    // Include PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    // Generate the QR code and save it as a PNG image
    $qrCodeFilePath = 'path_to_qr_code.png';
    $qrCode = new \Endroid\QrCode\QrCode($receiptNumber);
    $qrCode->setSize(300);
    $qrCode->setMargin(10);
    $qrCode->writeFile($qrCodeFilePath);  // Save the QR code as a file

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com';
        $mail->Password = 'akbbhmrrxzryovqt'; // Make sure to use a secure method to store the password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set email sender and recipient
        $mail->setFrom('mail.blackgym@gmail.com', 'Black Gym');
        $mail->addAddress($email, $name);

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful';
        $mail->Body = "<p>Dear $name,</p><p>Please find your payment receipt attached along with your payment QR code.</p><p>Thank you for being a member of Black Gym!</p>";

        // Attach the PDF receipt
        $mail->addStringAttachment($pdfContent, 'PaymentReceipt.pdf');

        // Attach the QR Code image
        $mail->addAttachment($qrCodeFilePath, 'PaymentQRCode.png');

        // Send the email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }

    // Optionally, delete the QR code file after sending the email (clean up)
    unlink($qrCodeFilePath);
}