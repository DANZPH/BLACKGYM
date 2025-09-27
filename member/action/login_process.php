<?php
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $email = $_POST["email"];
     $password = $_POST["password"];

     // Check if the member exists by joining Users and Members tables
     $stmt = $conn1->prepare("
         SELECT u.UserID, u.Username, u.Password, u.Verified, m.MemberID
         FROM Users u
         INNER JOIN Members m ON u.UserID = m.UserID
         WHERE u.Email = ?
     ");
     $stmt->bind_param("s", $email);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows > 0) {
         // Member found, fetch data
         $member = $result->fetch_assoc();

         // Check if password is correct
         if (password_verify($password, $member['Password'])) {
             // Check if the member is verified
             if ($member['Verified'] == 1) {
                 // Member is valid, set session variables
                 $_SESSION['MemberID'] = $member['MemberID'];
                 $_SESSION['username'] = $member['Username'];
                 $_SESSION['UserID'] = $member['UserID'];

                 // Redirect to member dashboard
                 header('Location: ../dashboard/index.php');
                 exit();
             } else {
                 // Member is not verified
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
         // No member found
         $_SESSION['error'] = "Invalid email or password.";
         header('Location: ../../member/login.php');
         exit();
     }
} else {
    // If not a POST request, redirect to login page
    header('Location: ../../member/login.php');
    exit();
}