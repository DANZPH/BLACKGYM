<?php
session_start();
include '../../database/connection.php';

// Check if the user is already logged in and the session is active
if (isset($_SESSION['MemberID'])) {
    // Fetch the MemberID from session
    $memberID = $_SESSION['MemberID'];

    // Query to fetch the latest ReceiptNumber for the logged-in user
    $receipt_query = $conn1->prepare("SELECT ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC LIMIT 1");
    $receipt_query->bind_param("i", $memberID);
    $receipt_query->execute();
    $receipt_result = $receipt_query->get_result();

    // Check if the result is valid
    if ($receipt_result->num_rows > 0) {
        $receipt = $receipt_result->fetch_assoc();
        // Store the latest ReceiptNumber in session
        $_SESSION['latestReceiptNumber'] = $receipt['ReceiptNumber'];
    } else {
        // If no receipt is found, set the session variable to null
        $_SESSION['latestReceiptNumber'] = null;
    }

    // Now, you can use the latest ReceiptNumber anywhere in your code
    // Example: Fetch the latest receipt number
    $latestReceiptNumber = $_SESSION['latestReceiptNumber'] ?? null;

    // Optionally, you can display or use the QR code generation logic here
    // Example: Redirect to dashboard or any page after retrieving the receipt number
    header('Location: ../dashboard/index.php'); // Adjust as per your logic
    exit();
} else {
    // If the user is not logged in, redirect to login page
    $_SESSION['error'] = "You must be logged in to access this page.";
    header('Location: ../../member/login.php');
    exit();
}
?>