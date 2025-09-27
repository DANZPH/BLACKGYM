<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'] ?? 'Cash';
    $amount = floatval($_POST['amount']);
    $amountPaid = floatval($_POST['amountPaid']);
    $addToBalance = $_POST['addToBalance'] ?? 'no';
    $paymentMethod = $paymentType; // Use paymentType as paymentMethod
    
    $changeAmount = $amountPaid - $amount;

    $conn1->begin_transaction();

    try {
        // Insert payment record using our actual database schema
        $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, Amount, PaymentMethod, PaymentType) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $memberID, $amount, $paymentMethod, $paymentType);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment: " . $stmt->error);
        }

        // Get current member balance
        $balanceQuery = $conn1->prepare("SELECT Balance FROM Members WHERE MemberID = ?");
        $balanceQuery->bind_param("i", $memberID);
        $balanceQuery->execute();
        $balanceResult = $balanceQuery->get_result();
        $currentBalance = 0;
        
        if ($balanceResult->num_rows > 0) {
            $balanceRow = $balanceResult->fetch_assoc();
            $currentBalance = floatval($balanceRow['Balance']);
        }

        // Calculate new balance based on addToBalance option
        $newBalance = $currentBalance;
        
        if ($addToBalance === 'yes') {
            // Add change to balance
            $newBalance = $currentBalance + $changeAmount;
        } elseif ($addToBalance === 'withdraw') {
            // Withdraw all balance
            $newBalance = 0;
        }
        // If 'no', balance remains the same

        // Update member balance and status
        $updateMemberStmt = $conn1->prepare("UPDATE Members SET Balance = ?, MembershipStatus = 'Active' WHERE MemberID = ?");
        $updateMemberStmt->bind_param("di", $newBalance, $memberID);
        
        if (!$updateMemberStmt->execute()) {
            throw new Exception("Error updating member: " . $updateMemberStmt->error);
        }

        // Update membership status to Active
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("i", $memberID);
        $updateMembershipStmt->execute(); // Don't throw error if membership doesn't exist

        // Commit transaction
        $conn1->commit();
        
        echo "Payment successfully processed! New balance: " . number_format($newBalance, 2);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn1->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
