<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

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
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Amount paid cannot be less than the amount!',
                });
              </script>";
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
                // Success message
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Processed',
                            text: 'Payment was successfully processed, and membership status updated to Active!',
                        }).then(function() {
                            window.location = 'your_redirect_page.php'; // Replace with your desired page
                        });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating member status!',
                        });
                      </script>";
            }

            // Close the update statement for Membership
            $updateMembershipStmt->close();
            // Close the update statement for Member
            $updateMemberStmt->close();
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating membership status!',
                    });
                  </script>";
        }
    } else {
        // Error, return an error message
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Error',
                    text: 'Error processing payment!',
                });
              </script>";
    }

    // Close the insert statement
    $stmt->close();
}

$conn1->close();
?>