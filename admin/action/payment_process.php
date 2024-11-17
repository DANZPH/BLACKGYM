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

    // Begin a transaction to ensure both inserts are successful
    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

        if ($stmt->execute()) {
            // Payment successful, now update the 'Members' and 'Membership' tables

            // Update 'Status' in 'Members' table to 'active'
            $updateMembers = $conn1->prepare("UPDATE Members SET Status = 'Active' WHERE MemberID = ?");
            $updateMembers->bind_param("i", $memberID);
            $updateMembers->execute();

            // Update 'MembershipStatus' in 'Membership' table to 'active'
            $updateMembership = $conn1->prepare("UPDATE Membership SET MembershipStatus = 'Active' WHERE MemberID = ?");
            $updateMembership->bind_param("i", $memberID);
            $updateMembership->execute();

            // Commit the transaction
            $conn1->commit();

            // Return success message
            echo "Payment processed successfully!";
        } else {
            // Rollback the transaction in case of error
            $conn1->rollback();
            echo "Error processing payment: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // In case of exception, rollback the transaction
        $conn1->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $conn1->close();
}
?>