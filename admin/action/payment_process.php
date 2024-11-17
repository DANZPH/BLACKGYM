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

    // Start transaction to ensure all updates are made together
    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Update the Member's status to "Active" in the Members table
        $stmt_member = $conn1->prepare("UPDATE Members SET Status = ? WHERE MemberID = ?");
        $status = 'Active';
        $stmt_member->bind_param("si", $status, $memberID);

        if (!$stmt_member->execute()) {
            throw new Exception("Error updating member status: " . $stmt_member->error);
        }

        // Update the Membership's status to "Active" in the Membership table
        $stmt_membership = $conn1->prepare("UPDATE Membership SET MembershipStatus = ? WHERE MemberID = ?");
        $stmt_membership->bind_param("si", $status, $memberID);

        if (!$stmt_membership->execute()) {
            throw new Exception("Error updating membership status: " . $stmt_membership->error);
        }

        // Commit the transaction
        $conn1->commit();

        // Success, return a success message
        echo "Payment processed successfully!";
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn1->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close statements
    $stmt->close();
    $stmt_member->close();
    $stmt_membership->close();
}

$conn1->close();
?>