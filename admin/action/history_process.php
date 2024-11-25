<?php
include '../../database/connection.php';

if (isset($_GET['MemberID']) && is_numeric($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    // Fetch payment history
    $sql = "SELECT Amount, AmountPaid, (AmountPaid - Amount) AS ChangeAmount, PaymentType, PaymentDate, ReceiptNumber 
            FROM Payments 
            WHERE MemberID = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-bordered'>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Amount Paid</th>
                        <th>Change</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Receipt Number</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>₱" . number_format($row['Amount'], 2) . "</td>
                    <td>₱" . number_format($row['AmountPaid'], 2) . "</td>
                    <td>₱" . number_format($row['ChangeAmount'], 2) . "</td>
                    <td>{$row['PaymentType']}</td>
                    <td>{$row['PaymentDate']}</td>
                    <td>{$row['ReceiptNumber']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='text-center'>No payment history found for this member.</p>";
    }
} else {
    echo "<p class='text-center'>Invalid Member ID.</p>";
}
?>