<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.9/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <div class="col-md-9 content-wrapper">
            <h2 class="mb-4">Member Payments</h2>
            <div class="card">
                <div class="card-header">
                    <h5>Members Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="paymentsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Membership Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.MembershipStatus
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['MembershipStatus']}</td>
                                            <td><button class='btn btn-primary pay-btn' data-memberid='{$row['MemberID']}'>Pay</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No members found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="paymentForm">
            <div class="form-group">
                <label for="paymentType">Payment Type</label>
                <input type="text" class="form-control" id="paymentType" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" required>
            </div>
            <div class="form-group">
                <label for="amountPaid">Amount Paid</label>
                <input type="number" class="form-control" id="amountPaid" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="processPayment">Pay</button>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.9/dist/sweetalert2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#paymentsTable').DataTable();

    $('.pay-btn').click(function () {
        var memberID = $(this).data('memberid');
        $('#paymentModal').modal('show');

        $('#processPayment').click(function() {
            var paymentType = $('#paymentType').val();
            var amount = $('#amount').val();
            var amountPaid = $('#amountPaid').val();

            $.ajax({
                url: '../action/payment_process.php',
                type: 'POST',
                data: {
                    memberID: memberID,
                    paymentType: paymentType,
                    amount: amount,
                    amountPaid: amountPaid
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Processed',
                            text: data.message