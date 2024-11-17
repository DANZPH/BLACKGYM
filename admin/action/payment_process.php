<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    
    // Calculate the change (if any)
    $changeAmount = $amountPaid - $amount;

    // Check if the amount paid is sufficient
    if ($amountPaid < $amount) {
        echo "Error: Amount paid cannot be less than the amount.";
        exit();
    }

    // Insert the payment details into the Payments table
    $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                             VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

    if ($stmt->execute()) {
        // Success, return a success message
        echo "Payment processed successfully!";
    } else {
        // Error, return an error message
        echo "Error processing payment: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

$conn1->close();
?>