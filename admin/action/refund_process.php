<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php'); 
    exit();
}

include '../../database/connection.php';

// Get POST data from the AJAX request
$memberID = $_POST['memberID'];

if (empty($memberID)) {
    echo "Error: Member ID is missing.";
    exit();
}

// Get the current date (Asia/Manila timezone)
date_default_timezone_set('Asia/Manila');
$currentDate = date('Y-m-d H:i:s');

// Fetch the member's membership details (EndDate, current balance, and subscription)
$query = "SELECT m.EndDate, m.Status, m.Subscription, mem.Balance, m.StartDate
          FROM Membership m
          JOIN Members mem ON m.MemberID = mem.MemberID
          WHERE m.MemberID = ? AND m.Status = 'Active'";

$stmt = $conn1->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn1->error);  // Show SQL errors if there's an issue with the query
}

$stmt->bind_param('i', $memberID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Get the membership data
    $row = $result->fetch_assoc();
    $endDate = $row['EndDate'];
    $startDate = $row['StartDate'];
    $subscription = $row['Subscription'];
    $currentBalance = $row['Balance'];

    // Ensure EndDate is not null and the current date is before the EndDate
    if ($endDate && $currentDate < $endDate) {
        // Create DateTime objects for current date, StartDate, and EndDate
        $currentDateObj = new DateTime($currentDate);
        $endDateObj = new DateTime($endDate);
        $startDateObj = new DateTime($startDate);

        // Calculate the total days in the subscription period (from StartDate to EndDate)
        $totalDays = $startDateObj->diff($endDateObj)->days;

        // Calculate the daily rate
        $dailyRate = $subscription / $totalDays;

        // Calculate the remaining days (from currentDate to EndDate)
        $remainingDays = $currentDateObj->diff($endDateObj)->days;

        // If remaining days is negative or zero, set refund to zero
        if ($remainingDays <= 0) {
            echo "Error: No valid remaining period for refund.";
            exit();
        }

        // Calculate the refund amount (based on remaining days * daily rate)
        $refundAmount = $dailyRate * $remainingDays;

        // Update the balance (add the refund amount to the member's balance)
        $newBalance = $currentBalance + $refundAmount;

        // Update the member's balance in the Members table
        $updateBalanceQuery = "UPDATE Members SET Balance = ? WHERE MemberID = ?";
        $updateBalanceStmt = $conn1->prepare($updateBalanceQuery);
        if ($updateBalanceStmt === false) {
            die('MySQL prepare error: ' . $conn1->error);  // Show SQL errors if there's an issue with the query
        }
        $updateBalanceStmt->bind_param('di', $newBalance, $memberID);
        $updateBalanceStmt->execute();

        // Update the membership status to 'Expired' after the refund
        $updateMembershipQuery = "UPDATE Membership SET Status = 'Expired' WHERE MemberID = ?";
        $updateMembershipStmt = $conn1->prepare($updateMembershipQuery);
        if ($updateMembershipStmt === false) {
            die('MySQL prepare error: ' . $conn1->error);  // Show SQL errors if there's an issue with the query
        }
        $updateMembershipStmt->bind_param('i', $memberID);
        $updateMembershipStmt->execute();

        // Set the EndDate to the current date
        $updateEndDateQuery = "UPDATE Membership SET EndDate = ? WHERE MemberID = ?";
        $updateEndDateStmt = $conn1->prepare($updateEndDateQuery);
        if ($updateEndDateStmt === false) {
            die('MySQL prepare error: ' . $conn1->error);  // Show SQL errors if there's an issue with the query
        }
        $updateEndDateStmt->bind_param('si', $currentDate, $memberID);
        $updateEndDateStmt->execute();

        // Update the member's status to 'Inactive' after refund
        $updateMemberStatusQuery = "UPDATE Members SET MembershipStatus = 'Inactive' WHERE MemberID = ?";
        $updateMemberStatusStmt = $conn1->prepare($updateMemberStatusQuery);
        if ($updateMemberStatusStmt === false) {
            die('MySQL prepare error: ' . $conn1->error);  // Show SQL errors if there's an issue with the query
        }
        $updateMemberStatusStmt->bind_param('i', $memberID);
        $updateMemberStatusStmt->execute();

        // Return a success response
        echo "Refund of " . number_format($refundAmount, 2) . " processed successfully. New balance: " . number_format($newBalance, 2);
    } else {
        echo "Error: Subscription has already expired or EndDate is invalid.";
    }
} else {
    echo "Error: Membership not found or not active.";
}

// Close the database connection
$stmt->close();
$conn1->close();
?>
