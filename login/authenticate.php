<?php
session_start();
require '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are set
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Prepare and execute SQL statement to fetch user details based on email
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            // User found, verify password
            $row = $result->fetch_assoc();
            if ($password === $row['Password']) {
                // Password correct, set session email and redirect to dashboard
                $_SESSION['email'] = $email;
                echo "success";
            } else {
                // Invalid password
                echo "error";
            }
        } else {
            // User not found
            echo "error";
        }
    } else {
        // Missing email or password
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}
?>

