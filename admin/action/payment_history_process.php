<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Fetch all members for the dropdown
if (isset($_GET['fetch_members']) && $_GET['fetch_members'] === 'true') {
    $query = "SELECT MemberID, CONCAT(UserID, ' - ', Address) AS MemberName FROM Members";
    $result = $conn1->query($query);

    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $members]);
    exit();
}

// Fetch payment history for a specific member
if (isset($_GET['MemberID']) && is_numeric($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    $query = "
        SELECT p.PaymentType, p.Amount, p.AmountPaid, p.ChangeAmount, 
               p.PaymentDate, m.StartDate, m.EndDate 
        FROM Payments p
        LEFT JOIN Membership m ON p.MemberID = m.MemberID
        WHERE p.MemberID = ?";
    $stmt = $conn1->prepare($query);
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $payments
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);