<?php
session_start();  // Start the session to access session variables
include '../database/connection.php';  // Assuming connection.php sets up $conn1

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and OTP are provided
    if (isset($_POST["email"]) && isset($_POST["otp"])) {
        $email = $_POST["email"];
        $otp = $_POST["otp"];

        // Prepare SQL to check if the OTP is correct and not expired
        $stmt = $conn1->prepare("SELECT UserID, OTP, OTPExpiration, Verified FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
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
                // Check if the OTP matches
                if ($otp === $user['OTP']) {
                    // Mark the user as verified
                    $stmt = $conn1->prepare("UPDATE Users SET Verified = true WHERE Email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->close();

                    // Now, let's fetch the MemberID and set it in the session
                    $stmt = $conn1->prepare("SELECT MemberID FROM Members WHERE UserID = ?");
                    $stmt->bind_param("i", $user['UserID']);
                    $stmt->execute();
                    $memberResult = $stmt->get_result();
                    $stmt->close();

                    if ($memberResult->num_rows > 0) {
                        $member = $memberResult->fetch_assoc();
                        $_SESSION['MemberID'] = $member['MemberID'];  // Set MemberID session
                        $_SESSION['username'] = $user['Username'];  // Optionally, set username session variable

                        // Send success message
                        echo "OTP verified successfully!";
                    } else {
                        echo "Error: Member not found.";
                    }
                } else {
                    echo "Error: Invalid OTP.";
                }
            }
        } else {
            echo "Error: No user found with this email.";
        }
    } else {
        echo "Error: Email and OTP are required.";
    }
} else {
    // Redirect if it's not a POST request
    echo "Error: Invalid request method.";
}
?>