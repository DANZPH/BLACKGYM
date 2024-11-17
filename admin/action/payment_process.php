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

    // Start a transaction to ensure both updates are successful before committing
    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Update the status in the Members table to 'active'
        $stmt = $conn1->prepare("UPDATE Members SET Status = 'active' WHERE MemberID = ?");
        $stmt->bind_param("d", $memberID);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating Member status: " . $stmt->error);
        }

        // Update the MembershipStatus in the Membership table to 'active'
        $stmt = $conn1->prepare("UPDATE Membership SET MembershipStatus = 'active' WHERE MemberID = ?");
        $stmt->bind_param("d", $memberID);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating Membership status: " . $stmt->error);
        }

        // Commit the transaction if all updates are successful
        $conn1->commit();
        echo "Payment processed successfully and statuses updated!";

    } catch (Exception $e) {
        // If any error occurs, roll back the transaction
        $conn1->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the statement
    $stmt->close();
}

$conn1->close();
?>