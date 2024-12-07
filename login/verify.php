<?php
include '../database/connection.php';  // Assuming connection.php sets up $conn1

session_start();  // Start the session

function verifyOTP($email, $otp, $conn1) {
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
            return "Error: OTP has expired.";
        } else {
            // Update Verified status in the database
            $stmt = $conn1->prepare("UPDATE Users SET Verified = true WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            // Fetch MemberID from the Members table based on the verified user
            $stmt = $conn1->prepare("SELECT MemberID FROM Members WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($memberID);
            $stmt->fetch();
            $stmt->close();

            // Check if MemberID is found
            if ($memberID) {
                // Store MemberID in the session for auto-login
                $_SESSION['MemberID'] = $memberID;

                // Optionally, you can redirect the user to the dashboard or a different page
                header("Location: dashboard.php");  // Replace with your desired page
                exit();
            } else {
                return "Error: Member not found.";
            }

            return "OTP verified successfully!";
        }
    } else {
        return "Error: Invalid OTP.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and OTP are provided
    if (isset($_POST["email"]) && isset($_POST["otp"])) {
        $email = $_POST["email"];
        $otp = $_POST["otp"];

        // Call the verifyOTP function
        $result = verifyOTP($email, $otp, $conn1);

        echo $result;  // Display the result (error message or success)
    } else {
        echo "Error: Email and OTP are required.";
    }
}
?>