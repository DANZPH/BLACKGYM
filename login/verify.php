<?php
session_start();  // Start the session

include '../database/connection.php';  // Assuming connection.php sets up $conn1

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and OTP are provided
    if (isset($_POST["email"]) && isset($_POST["otp"])) {
        $email = $_POST["email"];
        $otp = $_POST["otp"];

        // Verify OTP and check expiration
        $stmt = $conn1->prepare("SELECT * FROM Users WHERE Email = ? AND OTP = ?");
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $otpExpiration = $user['OTPExpiration'];  // Get the OTP expiration time

            // Check if OTP has expired
            if (strtotime($otpExpiration) < time()) {
                echo "Error: OTP has expired.";
            } else {
                // Update Verified status in the database
                $stmt = $conn1->prepare("UPDATE Users SET Verified = true WHERE Email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->close();

                // Set session variables for the logged-in user
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];  // You can add more session variables if needed

                echo "OTP verified successfully!";
            }
        } else {
            echo "Error: Invalid OTP.";
        }
    } else {
        echo "Error: Email and OTP are required.";
    }
}
?>
