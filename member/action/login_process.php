<?php
// action/login_process.php

session_start(); // Start the session to track the user

// Include connection file
include '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if the user exists in the database
    $stmt = $conn1->prepare("SELECT UserID, Username, Password, Verified FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User found, fetch data
        $user = $result->fetch_assoc();
        
        // Check if password is correct
        if (password_verify($password, $user['Password'])) {
            // Check if the user is verified
            if ($user['Verified'] == 1) {
                // Correct password, user verified, set session variables
                $_SESSION['userID'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                
                // Redirect to member dashboard or main page
                header('Location: ../../member/dashboard/index.php');
                exit();
            } else {
                // User is not verified
                $_SESSION['error'] = "Your account is not verified. Please check your email for the verification link.";
                header('Location: ../../member/login.php');
                exit();
            }
        } else {
            // Incorrect password
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: ../../member/login.php');
            exit();
        }
    } else {
        // No user found
        $_SESSION['error'] = "Invalid email or password.";
        header('Location: ../../member/login.php');
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header('Location: ../../member/login.php');
    exit();
}