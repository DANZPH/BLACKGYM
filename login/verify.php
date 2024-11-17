<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and OTP are provided
    if (isset($_POST["email"]) && isset($_POST["otp"])) {
        $email = $_POST["email"];
        $otp = $_POST["otp"];

        // Database credentials
        $host = "sql104.infinityfree.com"; // Change this to your database host
        $dbname = "if0_36048499_gymr"; // Change this to your database name
        $username = "if0_36048499"; // Change this to your database username
        $password = "LokK4Hhvygq"; // Change this to your database password

        // Database connection
        $conn = new mysqli($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Verify OTP and check expiration
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ? AND OTP = ?");
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
                $stmt = $conn->prepare("UPDATE Users SET Verified = true WHERE Email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->close();

                echo "OTP verified successfully!";
            }
        } else {
            echo "Error: Invalid OTP.";
        }

        $conn->close();
    } else {
        echo "Error: Email and OTP are required.";
    }
}
?>
