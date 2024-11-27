<?php
session_start();  // Start the session to store session variables
include '../../database/connection.php';  // Include the database connection

// Check if the user is logged in
if (isset($_SESSION['MemberID'])) {
    $memberID = $_SESSION['MemberID']; // Retrieve the MemberID from the session

    // Query to fetch the latest ReceiptNumber for the logged-in member
    $receipt_query = $conn1->prepare("SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1");
    $receipt_query->bind_param("i", $memberID);
    $receipt_query->execute();
    $receipt_result = $receipt_query->get_result();

    // Check if a receipt is found
    if ($receipt_result->num_rows > 0) {
        $receipt = $receipt_result->fetch_assoc();
        $_SESSION['latestReceiptNumber'] = $receipt['ReceiptNumber']; // Store the latest receipt number in session
    } else {
        $_SESSION['latestReceiptNumber'] = null; // No receipt found for this member
    }
} else {
    // If not logged in, redirect to the login page
    header('Location: ../../member/login.php');
    exit();
}
?>