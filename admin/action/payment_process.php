<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = intval($_POST['memberID']);
    $amount = 100.00; // Example payment amount
    $paymentMethod = 'Cash'; // Example payment method

    // Start the transaction
    $conn->begin_transaction();

    try {
        // Insert payment record
        $sql = "INSERT INTO Payments (MemberID, Amount, PaymentMethod) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ids", $memberID, $amount, $paymentMethod);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting payment.");
        }

        // Update membership status
        $sqlUpdate = "UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $memberID);

        if (!$stmtUpdate->execute()) {
            throw new Exception("Error updating membership status.");
        }

        // Commit the transaction
        $conn->commit();
        echo "Payment processed and membership activated successfully for Member ID: $memberID.";

    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    $stmt->close();
    $stmtUpdate->close();
    $conn->close();
}
?>