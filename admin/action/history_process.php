<?php
include '../../database/connection.php';

if (isset($_GET['MemberID']) && is_numeric($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    // Fetch history details
    $sql = "SELECT * FROM Payments WHERE MemberID = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>Payment ID: {$row['PaymentID']} | Amount: ₱{$row['Amount']} | Date: {$row['PaymentDate']}</li>";
        }
        echo "</ul>";
    } else {
        echo "No history found for this member.";
    }
} else {
    echo "Invalid Member ID.";
}
?>