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

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert payment record
        $sql_payment = "INSERT INTO Payments (MemberID, Amount, PaymentMethod) VALUES (?, ?, ?)";
        $stmt_payment = $conn->prepare($sql_payment);
        $stmt_payment->bind_param("ids", $memberID, $amount, $paymentMethod);
        $stmt_payment->execute();
        
        // Update membership status to 'Active'
        $sql_membership = "UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?";
        $stmt_membership = $conn->prepare($sql_membership);
        $stmt_membership->bind_param("i", $memberID);
        $stmt_membership->execute();

        // Commit the transaction
        $conn->commit();

        echo "Payment processed successfully for Member ID: $memberID and membership status updated to Active.";
    } catch (Exception $e) {
        // Rollback the transaction if anything goes wrong
        $conn->rollback();
        echo "Error processing payment: " . $e->getMessage();
    }

    // Clean up
    $stmt_payment->close();
    $stmt_membership->close();
    $conn->close();
}
?>