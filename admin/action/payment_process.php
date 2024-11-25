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

    // Begin transaction to ensure atomicity
    $conn1->begin_transaction();

    try {
        // Insert the payment details into the Payments table
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                                 VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Update the Members table to set MembershipStatus to 'Active'
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("d", $memberID);

        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating membership status in Members: " . $updateMemberStmt->error);
        }

        // Update the Membership table to set Status to 'Active'
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("d", $memberID);

        if (!$updateMembershipStmt->execute()) {
            throw new Exception("Error updating membership status in Membership: " . $updateMembershipStmt->error);
        }

        // Get the Subscription value (which will be used to calculate the EndDate)
        $subscription = $amount;

        // Calculate the number of months based on the Subscription value (Subscription ÷ 600)
        $numMonths = $subscription / 600;

        // Ensure that numMonths is an integer value (rounding if necessary)
        $numMonths = floor($numMonths);

        // Calculate the new EndDate by adding the number of months to the current date
        // Correcting the logic to handle proper month addition.
        $endDate = date('Y-m-d H:i:s', strtotime("+$numMonths months"));

        // Update the Membership table to set the calculated EndDate
        $updateEndDateStmt = $conn1->prepare("UPDATE Membership SET EndDate = ? WHERE MemberID = ?");
        $updateEndDateStmt->bind_param("sd", $endDate, $memberID);

        if (!$updateEndDateStmt->execute()) {
            throw new Exception("Error updating EndDate in Membership: " . $updateEndDateStmt->error);
        }

        // Commit transaction if all updates are successful
        $conn1->commit();

        echo "Payment processed, statuses updated to Active, and EndDate set based on subscription!";
    } catch (Exception $e) {
        // Rollback transaction on any error
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Close the statements
    $stmt->close();
    $updateMemberStmt->close();
    $updateMembershipStmt->close();
    $updateEndDateStmt->close();
}

$conn1->close();
?>