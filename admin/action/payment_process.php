l<?php
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
        // After inserting the payment, update the membership status and status to "Active"
        $updateStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active', Status = 'Active' WHERE MemberID = ?");
        $updateStmt->bind_param("i", $memberID);

        if ($updateStmt->execute()) {
            // Success, return a success message
            echo "Payment processed successfully and membership status updated to Active!";
        } else {
            echo "Error updating membership status: " . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        // Error inserting payment, return an error message
        echo "Error processing payment: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

$conn1->close();
?>