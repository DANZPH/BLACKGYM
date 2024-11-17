<?php
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['memberID'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['paymentMethod'];
    $notes = $_POST['notes'];

    $sql = "INSERT INTO Payments (MemberID, Amount, PaymentMethod, Notes) VALUES (?, ?, ?, ?)";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("idss", $memberID, $amount, $paymentMethod, $notes);

    if ($stmt->execute()) {
        echo "Payment added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>