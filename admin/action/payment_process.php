<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the necessary data from the POST request
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];  // Payment type (Cash, Credit, etc.)
    $amount = $_POST['amount'];            // Total amount for payment
    $amountPaid = $_POST['amountPaid'];    // Amount paid by the member

    // Calculate the change amount (if any)
    $changeAmount = $amountPaid - $amount;

    // Check if the payment is enough (amount paid should be >= amount)
    if ($amountPaid >= $amount) {
        // Begin the transaction to ensure atomicity
        $conn1->begin_transaction();

        try {
            // Insert payment details into the Payments table
            $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, Amount, PaymentMethod, PaymentDate) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("ids", $memberID, $amount, $paymentType);
            $stmt->execute();

            // Update the Members table Status to 'active'
            $stmt1 = $conn1->prepare("UPDATE Members SET Status = 'active' WHERE MemberID = ?");
            $stmt1->bind_param("i", $memberID);
            $stmt1->execute();

            // Update the Membership table MembershipStatus to 'active'
            $stmt2 = $conn1->prepare("UPDATE Membership SET MembershipStatus = 'active' WHERE MemberID = ?");
            $stmt2->bind_param("i", $memberID);
            $stmt2->execute();

            // Commit the transaction
            $conn1->commit();

            // Return success response
            echo json_encode(["status" => "success", "message" => "Payment processed successfully.", "change" => $changeAmount]);
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $conn1->rollback();
            echo json_encode(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
        }
    } else {
        // If amount paid is less than the amount, return an error
        echo json_encode(["status" => "error", "message" => "Insufficient amount paid. Please pay the full amount."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>