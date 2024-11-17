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

    // Begin transaction to ensure consistency
    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);
        $stmt->execute();

        // Update the MembershipStatus in the Membership table to 'active'
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET MembershipStatus = 'active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("d", $memberID);
        $updateMembershipStmt->execute();

        // Update the Status in the Members table to 'active'
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET Status = 'active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);
        $updateMemberStmt->execute();

        // Commit the transaction
        $conn1->commit();

        // Success message
        echo "Payment processed successfully! Member is now active.";

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Close the prepared statements
    $stmt->close();
    $updateMembershipStmt->close();
    $updateMemberStmt->close();
}

$conn1->close();
?>