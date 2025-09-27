<?php
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if (isset($_GET['MemberID']) && is_numeric($_GET['MemberID'])) {
    $memberID = intval($_GET['MemberID']);

    // Fetch payment history using our actual database schema
    $sql = "SELECT Amount, PaymentMethod, PaymentType, PaymentDate, ReceiptNumber 
            FROM Payments 
            WHERE MemberID = ? 
            ORDER BY PaymentDate DESC";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-bordered'>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Receipt Number</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            $paymentMethod = $row['PaymentMethod'] ?? 'Cash';
            $paymentType = $row['PaymentType'] ?? 'Subscription';
            $receiptNumber = $row['ReceiptNumber'] ?? 'N/A';
            
            echo "<tr>
                    <td>₱" . number_format($row['Amount'], 2) . "</td>
                    <td>{$paymentMethod}</td>
                    <td>{$paymentType}</td>
                    <td>" . date('M d, Y H:i', strtotime($row['PaymentDate'])) . "</td>
                    <td>{$receiptNumber}</td>
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