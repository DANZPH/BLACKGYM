<?php
// action/login_process.php

session_start(); // Start the session to track the user

// Include connection file
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // Get the username/email and password from the form
     $input = $_POST["email"]; // Can be either email or username
     $password = $_POST["password"];

     // Check if the admin exists in the Admins table directly
     $stmt = $conn1->prepare("
         SELECT a.AdminID, a.UserID, u.Username, u.Password, u.Verified, u.Email
         FROM Admins a
         INNER JOIN Users u ON a.UserID = u.UserID
         WHERE u.Email = ? OR u.Username = ?
     ");
     $stmt->bind_param("ss", $input, $input); // Bind the input to both email and username
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows > 0) {
         // Admin found, fetch data
         $admin = $result->fetch_assoc();

         // Check if password is correct
         if (password_verify($password, $admin['Password'])) {
             // Check if the admin is verified
             if ($admin['Verified'] == 1) {
                 // Admin is valid, set session variables
                 $_SESSION['AdminID'] = $admin['AdminID'];
                 $_SESSION['username'] = $admin['Username'];
                 $_SESSION['UserID'] = $admin['UserID'];

                 // Set remember me cookie if requested
                 if (isset($_POST['remember'])) {
                     setcookie('AdminLogin', $admin['AdminID'], time() + (86400 * 30), "/"); // 30 days
                 }

                 // Redirect to admin dashboard
                 header('Location: ../dashboard/index.php');
                 exit();
             } else {
                 // Admin is not verified
                 $_SESSION['error'] = "Your admin account is not verified. Please contact the system administrator.";
                 header('Location: ../../admin/login.php');
                 exit();
             }
         } else {
             // Incorrect password
             $_SESSION['error'] = "Invalid username/email or password.";
             header('Location: ../../admin/login.php');
             exit();
         }
     } else {
         // No admin found
         $_SESSION['error'] = "Invalid username/email or password.";
         header('Location: ../../admin/login.php');
         exit();
     }
} else {
    // If not a POST request, redirect to login page
    header('Location: ../../admin/login.php');
    exit();
}