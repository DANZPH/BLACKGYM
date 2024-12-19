<?php
include '../../database/connection.php'; 

if (isset($_GET['memberID'])) {
    $memberID = $_GET['memberID'];

    // Query to get the current balance (you might need to adjust this based on your actual table structure)
    $stmt = $conn1->prepare("SELECT Balance FROM Members WHERE MemberID = ?");
    $stmt->bind_param("d", $memberID);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    echo $balance;
}
?>
