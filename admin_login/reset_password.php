 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../login/phpmailer/src/Exception.php';
require '../login/phpmailer/src/PHPMailer.php';
require '../login/phpmailer/src/SMTP.php';
include '../database/connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if user exists in the database
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // User exists, generate reset token and expiration time
            $resetToken = generateResetToken();
            $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Token expiration time (1 hour from now)

            // Update the reset token and expiration time in the Users table
            $stmt = $conn->prepare("UPDATE Users SET ResetToken = ?, ResetTokenExpiration = ? WHERE Email = ?");
            $stmt->bind_param("sss", $resetToken, $resetTokenExpiration, $email);
            $stmt->execute();
            $stmt->close();

            // Send reset link via email
            $result = sendResetEmail($email, $resetToken);

            if ($result === true) {
                echo "Reset link sent to your email.";
            } else {
                echo "Error sending reset link: " . $result;
            }
        } else {
            echo "Error: Email not found.";
        }

        $conn->close();
    } else {
        echo "Error: Email is required.";
    }
}

function generateResetToken() {
    // Generate a random reset token
    return bin2hex(random_bytes(32));  // 64 characters long token
}

function sendResetEmail($email, $resetToken) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mail.blackgym@gmail.com'; // Your gmail
        $mail->Password = 'akbbhmrrxzryovqt'; // Your gmail app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('mail.blackgym@gmail.com'); // Your gmail
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Password';
        $mail->Body = 'Click the link below to reset your password: <a href="https://beta.dazx.xyz/login/reset.php?email=' . $email . '&token=' . $resetToken . '">Reset Password</a>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

?>