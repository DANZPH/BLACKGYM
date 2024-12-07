<?php
session_start(); 
$_SESSION['UserID'] = $userID;
$_SESSION['MemberID'] = $memberID;
include '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
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
                // Check if the user is an Admin
                $admin_check = $conn1->prepare("SELECT MemberID FROM Members WHERE UserID = ?");
                $admin_check->bind_param("i", $user['UserID']);
                $admin_check->execute();
                $admin_result = $admin_check->get_result();

                if ($admin_result->num_rows > 0) {
                    // User is an Admin, set session variables
                    $admin = $admin_result->fetch_assoc();
                    $_SESSION['MemberID'] = $admin['MemberID'];
                    $_SESSION['username'] = $user['Username'];
                    
                    // Redirect to member dashboard
                    header('Location: ../dashboard/index.php');
                    exit();
                } else {
                    // User is not an Admin
                    $_SESSION['error'] = "Access denied. Members only.";
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