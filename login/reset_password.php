 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

//require '../database/connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is set
    if (isset($_POST["email"])) {
        $email = $_POST["email"];

        // Database connection (adjust according to your setup)
        $host = "sql104.infinityfree.com"; // Change this to your database host
        $dbname = "if0_36048499_db_user"; // Change this to your database name
        $usernameDB = "if0_36048499"; // Change this to your database username
        $passwordDB = "LokK4Hhvygq"; // Change this to your database password

        $conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

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