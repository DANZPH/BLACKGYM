<?php
// pay_cancel.php
include 'db_connection.php'; // Include database connection file

// Fetch all pending payments with corresponding membership details for display
$query = "
    SELECT p.PaymentID, p.MemberID, p.Amount, p.PaymentDate, m.MembershipStatus, mem.Subscription, mem.Status as MembershipStatus, mem.StartDate, mem.EndDate
    FROM Payments p
    JOIN Members m ON p.MemberID = m.MemberID
    JOIN Membership mem ON m.MemberID = mem.MemberID
    WHERE m.MembershipStatus = 'Inactive'";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment</title>
</head>
<body>
    <h1>Process Payment</h1>
    <form action="payment_process.php" method="post">
        <label for="paymentID">Payment ID:</label>
        <select name="paymentID" id="paymentID" required>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['PaymentID']}'>Payment ID: {$row['PaymentID']} | MemberID: {$row['MemberID']} | Amount: {$row['Amount']} | Membership Status: {$row['MembershipStatus']}</option>";
            }
            ?>
        </select>
        <br><br>
        
        <h3>Membership Details:</h3>
        <label for="subscription">Subscription Amount:</label>
        <input type="text" id="subscription" name="subscription" disabled>
        <label for="status">Membership Status:</label>
        <input type="text" id="status" name="status" disabled>
        <label for="startDate">Start Date:</label>
        <input type="text" id="startDate" name="startDate" disabled>
        <label for="endDate">End Date:</label>
        <input type="text" id="endDate" name="endDate" disabled>
        <br><br>

        <button type="submit" name="action" value="pay">Pay</button>
        <button type="submit" name="action" value="cancel">Cancel</button>
    </form>

    <script>
        // Use JavaScript to populate the membership details when a payment is selected
        const paymentSelect = document.getElementById('paymentID');
        
        paymentSelect.addEventListener('change', function() {
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            const subscription = selectedOption.getAttribute('data-subscription');
            const status = selectedOption.getAttribute('data-status');
            const startDate = selectedOption.getAttribute('data-startdate');
            const endDate = selectedOption.getAttribute('data-enddate');
            
            document.getElementById('subscription').value = subscription;
            document.getElementById('status').value = status;
            document.getElementById('startDate').value = startDate;
            document.getElementById('endDate').value = endDate;
        });
    </script>
</body>
</html>