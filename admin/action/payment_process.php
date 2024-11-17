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
        // After payment insertion, update the MembershipStatus and Status to 'active'
        $updateStmt = $conn1->prepare("UPDATE Members 
                                       SET MembershipStatus = 'active', Status = 'active' 
                                       WHERE MemberID = ?");
        $updateStmt->bind_param("i", $memberID);
        
        if ($updateStmt->execute()) {
            echo "Payment processed successfully and status updated to active!";
        } else {
            echo "Payment processed, but error updating status.";
        }

        // Close the update statement
        $updateStmt->close();
    } else {
        // Error inserting payment details
        echo "Error processing payment: " . $stmt->error;
    }

    // Close the payment insert statement
    $stmt->close();
}

$conn1->close();
?>