<?php
include '../../database/connection.php';

if (isset($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    // Fetch payment history for the specified member
    $query = "SELECT PaymentID, PaymentType, Amount, AmountPaid, ChangeAmount, PaymentDate, ReceiptNumber 
              FROM Payments 
              WHERE MemberID = ?";
    $stmt = $conn1->prepare($query);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }

    // Return data as JSON
    echo json_encode($payments);
} else {
    echo json_encode(['error' => 'MemberID not provided.']);
}
?>