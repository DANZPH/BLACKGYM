<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['memberID'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $amountPaid = $_POST['amountPaid'];
    $changeAmount = $amountPaid - $amount; // Calculate the change

    // Update Membership Status to Active
    $updateMembershipStatus = "UPDATE Membership SET Status = 'Active' WHERE MemberID = $memberID";
    if ($conn1->query($updateMembershipStatus) === TRUE) {
        // Proceed with inserting the payment
        $insertPayment = "INSERT INTO Payments (MemberID, PaymentMethod, Amount, AmountPaid, PaymentDate) 
                          VALUES ('$memberID', '$paymentType', '$amount', '$amountPaid', NOW())";

        if ($conn1->query($insertPayment) === TRUE) {
            // Payment inserted successfully, show success message
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Payment of $' + '$amount' + ' has been processed.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                }).then(function() {
                    window.location.href = 'payments.php'; // Redirect to payments page
                });
            </script>
            ";
        } else {
            // If payment insertion fails, show error message
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue processing the payment. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            </script>
            ";
        }
    } else {
        // If membership status update fails, show error message
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'There was an issue updating the membership status. Please try again.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        </script>
        ";
    }
}
?>