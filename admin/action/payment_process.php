<?php
// pay_cancel.process.php
include 'db_connection.php'; // Include database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentID = $_POST['paymentID'];
    $action = $_POST['action'];

    if ($action === 'pay') {
        // Update payment and membership status
        $updatePaymentQuery = "
            UPDATE Payments 
            SET PaymentDate = NOW() 
            WHERE PaymentID = ?";
        
        $updateMembershipQuery = "
            UPDATE Membership 
            SET Status = 'Active', EndDate = DATE_ADD(NOW(), INTERVAL 1 MONTH) 
            WHERE MemberID = (
                SELECT MemberID FROM Payments WHERE PaymentID = ?
            )";
        
        // Prepare and execute the payment update
        $stmt = $conn->prepare($updatePaymentQuery);
        $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        $stmt->close();

        // Prepare and execute the membership update
        $stmt2 = $conn->prepare($updateMembershipQuery);
        $stmt2->bind_param("i", $paymentID);
        
        if ($stmt2->execute()) {
            echo "Payment processed successfully. Membership activated.";
        } else {
            echo "Error processing payment: " . $conn->error;
        }

        $stmt2->close();
    } elseif ($action === 'cancel') {
        // Cancel operation
        echo "Payment processing canceled.";
    }
}
$conn->close();
?>