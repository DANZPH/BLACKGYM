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
        // Error: Amount paid is less than required
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Amount paid cannot be less than the required amount!',
                }).then(function() {
                    window.location = 'your_redirect_page.php'; // Replace with the correct page to redirect after error
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
                // Success message - Show SweetAlert
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Processed',
                            text: 'Payment was successfully processed, and membership status updated to Active!',
                        }).then(function() {
                            window.location = 'your_redirect_page.php'; // Replace with the correct page
                        });
                      </script>";
                exit();
            } else {
                // Error: Failed to update member status
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating member status!',
                        }).then(function() {
                            window.location = 'your_redirect_page.php'; // Redirect on error
                        });
                      </script>";
                exit();
            }

            // Close the update statement for Membership
            $updateMembershipStmt->close();
            // Close the update statement for Member
            $updateMemberStmt->close();
        } else {
            // Error: Failed to update membership status
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating membership status!',
                    }).then(function() {
                        window.location = 'your_redirect_page.php'; // Redirect on error
                    });
                  </script>";
            exit();
        }
    } else {
        // Error: Failed to insert payment data
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Error',
                    text: 'Error processing payment!',
                }).then(function() {
                    window.location = 'your_redirect_page.php'; // Redirect on failure
                });
              </script>";
        exit();
    }

    // Close the insert statement
    $stmt->close();
}

$conn1->close();
?>