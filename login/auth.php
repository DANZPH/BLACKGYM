<?php
session_start();
include '../database/connection.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Fetch the user and member details based on the email and token
    $stmt = $conn1->prepare("SELECT u.UserID, m.MemberID FROM Users u INNER JOIN Members m ON u.UserID = m.UserID WHERE u.Email = ? AND u.ResetToken = ? AND u.ResetTokenExpiration > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Verify the email and update the status
        $stmt = $conn1->prepare("UPDATE Users SET Verified = 1, ResetToken = NULL, ResetTokenExpiration = NULL WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Fetch user and member IDs
        $user = $result->fetch_assoc();
        $_SESSION['UserID'] = $user['UserID'];  // Set UserID in session
        $_SESSION['MemberID'] = $user['MemberID'];  // Set MemberID in session

        // Redirect to the dashboard after successful login
        header('Location: ../member/dashboard/index.php');
        exit();
    } else {
        header('Location: ../member/dashboard/index.php');
    }
    $stmt->close();
}
?>