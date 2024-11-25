<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>

<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
            <h1 class="text-center mb-4 text-white">Member Payment History</h1>
            <p class="text-center text-white">Select a member to view their payment history.</p>

            <!-- Select Member -->
            <div class="form-group">
                <label for="memberSelect" class="text-white">Select Member:</label>
                <select class="form-control" id="memberSelect">
                    <option value="">-- Select a Member --</option>
                    <?php
                    // Fetch members for dropdown
                    $membersQuery = "SELECT MemberID, CONCAT(UserID, ' - ', MembershipStatus) AS MemberDetails FROM Members";
                    $membersResult = $conn1->query($membersQuery);
                    while ($member = $membersResult->fetch_assoc()) {
                        echo "<option value='{$member['MemberID']}'>{$member['MemberDetails']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Payment History Table -->
            <div class="table-responsive mt-4">
                <table id="paymentHistoryTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Payment Type</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Change</th>
                            <th>Payment Date</th>
                            <th>Receipt Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">

    <script>
        $(document).ready(function () {
            const paymentHistoryTable = $('#paymentHistoryTable').DataTable();

            // Load payment history when a member is selected
            $('#memberSelect').on('change', function () {
                const memberID = $(this).val();

                if (memberID) {
                    $.ajax({
                        url: '../action/fetch_payment_history.php',
                        method: 'GET',
                        data: { MemberID: memberID },
                        dataType: 'json',
                        success: function (data) {
                            paymentHistoryTable.clear();

                            if (!data.error) {
                                data.forEach(payment => {
                                    paymentHistoryTable.row.add([
                                        payment.PaymentID,
                                        payment.PaymentType,
                                        `₱${parseFloat(payment.Amount).toFixed(2)}`,
                                        `₱${parseFloat(payment.AmountPaid).toFixed(2)}`,
                                        `₱${parseFloat(payment.ChangeAmount).toFixed(2)}`,
                                        payment.PaymentDate,
                                        payment.ReceiptNumber
                                    ]);
                                });
                            }

                            paymentHistoryTable.draw();
                        },
                        error: function () {
                            alert('Failed to fetch payment history. Please try again.');
                        }
                    });
                } else {
                    paymentHistoryTable.clear().draw();
                }
            });
        });
    </script>
</body>
</html>