<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = intval($_POST['memberID']);
    $amount = 100.00; // Example payment amount
    $paymentMethod = 'Cash'; // Example payment method

    $sql = "INSERT INTO Payments (MemberID, Amount, PaymentMethod) VALUES (?, ?, ?)";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("ids", $memberID, $amount, $paymentMethod);

    if ($stmt->execute()) {
        echo "Payment processed successfully for Member ID: $memberID.";
    } else {
        echo "Error processing payment. Please try again.";
    }

    $stmt->close();
    $conn1->close();
}
?>