<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Validate the MemberID from the request
if (isset($_GET['MemberID']) && is_numeric($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    // Fetch member's payment history
    $query = "
        SELECT p.PaymentID, p.PaymentType, p.Amount, p.AmountPaid, p.ChangeAmount, 
               p.PaymentDate, m.StartDate, m.EndDate 
        FROM Payments p
        LEFT JOIN Membership m ON p.MemberID = m.MemberID
        WHERE p.MemberID = ?";
    $stmt = $conn1->prepare($query);
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Collect payments in an array
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $payments
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Member ID'
    ]);
}