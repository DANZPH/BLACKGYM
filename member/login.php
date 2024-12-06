<?php
session_start();

// Check if the user is logged in and session variables exist
if (!isset($_SESSION['user_id']) || !isset($_SESSION['member_id'])) {
    // If session is not set, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Use the session data to display member details
echo "Welcome, " . $_SESSION['username'];
echo "<br>Member ID: " . $_SESSION['member_id'];
?>
