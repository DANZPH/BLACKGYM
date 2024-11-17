<?php
include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['memberID'];
    $paymentMethod = $_POST['paymentMethod'];
    $amount = $_POST['amount'];
    $memberMoney = $_POST['memberMoney'];

    // Insert into the Payments table
    $sql = "INSERT INTO Payments (MemberID, PaymentMethod, Amount, PaymentDate) 
            VALUES (?, ?, ?, NOW())";

    if ($stmt = $conn1->prepare($sql)) {
        $stmt->bind_param("isds", $memberID, $paymentMethod, $amount, $memberMoney);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "error";
    }
}
?>