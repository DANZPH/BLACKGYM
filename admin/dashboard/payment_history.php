<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include '../../includes/head.php'; ?>

<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
            <h1 class="text-center mb-4 text-white">Payment History</h1>
            <p class="text-center text-white">View detailed payment history for a specific member.</p>

            <!-- Member Dropdown -->
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">
                    <label for="memberSelect" class="form-label text-white">Select Member:</label>
                    <select id="memberSelect" class="form-control">
                        <option value="" disabled selected>-- Select Member --</option>
                    </select>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="card-title text-center text-primary">Payment Records</h4>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Payment Type</th>
                                            <th>Amount</th>
                                            <th>Amount Paid</th>
                                            <th>Change</th>
                                            <th>Payment Date</th>
                                            <th>Membership Start</th>
                                            <th>Membership End</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentHistoryTable">
                                        <tr>
                                            <td colspan="7">No data available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Populate dropdown with members
            $.ajax({
                url: 'payment_history_process.php',
                method: 'GET',
                data: { fetch_members: 'true' },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success && result.data.length > 0) {
                        result.data.forEach(member => {
                            $('#memberSelect').append(
                                `<option value="${member.MemberID}">${member.MemberName}</option>`
                            );
                        });
                    }
                },
                error: function () {
                    alert('Failed to load members.');
                }
            });

            // Fetch payment history when a member is selected
            $('#memberSelect').on('change', function () {
                const memberID = $(this).val();

                if (memberID) {
                    $.ajax({
                        url: 'payment_history_process.php',
                        method: 'GET',
                        data: { MemberID: memberID },
                        success: function (response) {
                            const result = JSON.parse(response);
                            const tableBody = $('#paymentHistoryTable');
                            tableBody.empty();

                            if (result.success && result.data.length > 0) {
                                result.data.forEach(payment => {
                                    const row = `
                                        <tr>
                                            <td>${payment.PaymentType}</td>
                                            <td>₱${parseFloat(payment.Amount).toFixed(2)}</td>
                                            <td>₱${parseFloat(payment.AmountPaid).toFixed(2)}</td>
                                            <td>₱${parseFloat(payment.ChangeAmount).toFixed(2)}</td>
                                            <td>${payment.PaymentDate}</td>
                                            <td>${payment.StartDate || 'N/A'}</td>
                                            <td>${payment.EndDate || 'N/A'}</td>
                                        </tr>
                                    `;
                                    tableBody.append(row);
                                });
                            } else {
                                tableBody.append('<tr><td colspan="7">No data available</td></tr>');
                            }
                        },
                        error: function () {
                            alert('Failed to fetch payment history.');
                        }
                    });
                } else {
                    $('#paymentHistoryTable').html('<tr><td colspan="7">No data available</td></tr>');
                }
            });
        });
    </script>
</body>

</html>