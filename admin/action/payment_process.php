<?php
include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $change = $_POST['change'];

    // Insert payment into Payments table
    $sql = "INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount, PaymentDate) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("isdd", $memberID, $paymentType, $amount, $amountPaid, $change);

    if ($stmt->execute()) {
        // Update membership status if necessary (optional)
        // Example: Change status to 'Paid' or similar
        $updateStatusSQL = "UPDATE Members SET MembershipStatus = 'Paid' WHERE MemberID = ?";
        $stmtUpdate = $conn1->prepare($updateStatusSQL);
        $stmtUpdate->bind_param("i", $memberID);
        $stmtUpdate->execute();

        echo "Payment processed successfully!";
    } else {
        echo "Error processing payment. Please try again.";
    }
}
?>