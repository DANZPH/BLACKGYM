<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

$response = []; // Initialize response array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];

    // Calculate the change (if any)
    $changeAmount = $amountPaid - $amount;

    // Check if the amount paid is sufficient
    if ($amountPaid < $amount) {
        $response['status'] = 'error';
        $response['message'] = 'Amount paid cannot be less than the amount!';
        echo json_encode($response);  // Return JSON response
        exit();
    }

    // Insert the payment details into the Payments table
    $stmt = $conn1->prepare("INSERT INTO Payments (MemberID, PaymentType, Amount, AmountPaid, ChangeAmount) 
                             VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddd", $memberID, $paymentType, $amount, $amountPaid, $changeAmount);

    if ($stmt->execute()) {
        // Payment processed successfully, now update the Membership status to "Active"
        $updateMembershipStmt = $conn1->prepare("UPDATE Membership SET Status = 'Active' WHERE MemberID = ?");
        $updateMembershipStmt->bind_param("d", $memberID);

        if ($updateMembershipStmt->execute()) {
            // Also update the Member's MembershipStatus to 'Active'
            $updateMemberStmt = $conn1->prepare("UPDATE Members SET MembershipStatus = 'Active' WHERE MemberID = ?");
            $updateMemberStmt->bind_param("d", $memberID);

            if ($updateMemberStmt->execute()) {
                // Success response
                $response['status'] = 'success';
                $response['message'] = 'Payment was successfully processed, and membership status updated to Active!';
            } else {
                // Error updating member status
                $response['status'] = 'error';
                $response['message'] = 'Error updating member status!';
            }

            // Close the update statement for Membership
            $updateMembershipStmt->close();
            // Close the update statement for Member
            $updateMemberStmt->close();
        } else {
            // Error updating membership status
            $response['status'] = 'error';
            $response['message'] = 'Error updating membership status!';
        }
    } else {
        // Error processing payment
        $response['status'] = 'error';
        $response['message'] = 'Error processing payment!';
    }

    // Close the insert statement
    $stmt->close();
}

// Close the connection
$conn1->close();

// Return the response as JSON
echo json_encode($response);
?>