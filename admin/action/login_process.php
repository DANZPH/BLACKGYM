<?php
// action/login_process.php

session_start(); // Start the session to track the user

// Include connection file
include '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username/email and password from the form
    $input = $_POST["email"]; // Can be either email or username
    $password = $_POST["password"];

    // Check if the user exists in the Users table
    $stmt = $conn1->prepare("
        SELECT UserID, Username, Password, Verified 
        FROM Users 
        WHERE Email = ? OR Username = ?
    ");
    $stmt->bind_param("ss", $input, $input); // Bind the input to both email and username
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User found, fetch data
        $user = $result->fetch_assoc();
        
        // Check if password is correct
        if (password_verify($password, $user['Password'])) {
            // Check if the user is verified
            if ($user['Verified'] == 1) {
                // Check if the user is an Admin
                $admin_check = $conn1->prepare("SELECT AdminID FROM Admins WHERE UserID = ?");
                $admin_check->bind_param("i", $user['UserID']);
                $admin_check->execute();
                $admin_result = $admin_check->get_result();

                if ($admin_result->num_rows > 0) {
                    // User is an Admin, set session variables
                    $admin = $admin_result->fetch_assoc();
                    $_SESSION['AdminID'] = $admin['AdminID'];
                    $_SESSION['username'] = $user['Username'];
                    
                    // Redirect to admin dashboard
                    header('Location: ../dashboard/index.php');
                    exit();
                } else {
                    // User is not an Admin
                    $_SESSION['error'] = "Access denied. Admins only.";
                    header('Location: ../../member/login.php');
                    exit();
                }
            } else {
                // User is not verified
                $_SESSION['error'] = "Your account is not verified. Please check your email for the verification link.";
                header('Location: ../../member/login.php');
                exit();
            }
        } else {
            // Incorrect password
            $_SESSION['error'] = "Invalid username/email or password.";
            header('Location: ../../member/login.php');
            exit();
        }
    } else {
        // No user found
        $_SESSION['error'] = "Invalid username/email or password.";
        header('Location: ../../member/login.php');
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header('Location: ../../member/login.php');
    exit();
}