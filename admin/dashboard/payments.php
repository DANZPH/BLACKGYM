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
                                    <th style="display:none;">Member ID</th> <!-- Hidden column for MemberID -->
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Membership Status</th>
                                    <th>Subscription</th> <!-- New column for Subscription -->
                                    <th>Session Price</th> <!-- New column for Session Price -->
                                    <th>Total Bill</th> <!-- New column for Total Bill -->
                                    <th>Status</th> <!-- New column for Status -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.MembershipStatus, 
                                        Membership.Subscription, Membership.SessionPrice, 
                                        (Membership.Subscription + Membership.SessionPrice) AS TotalBill,
                                        CASE 
                                            WHEN Membership.Status = 'Active' THEN 'Active'
                                            WHEN Membership.Status = 'Pending' THEN 'Pending'
                                            WHEN Membership.Status = 'Expired' THEN 'Expired'
                                        END AS Status
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID
                                        LEFT JOIN Membership ON Members.MemberID = Membership.MemberID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td style='display:none;'>{$row['MemberID']}</td> <!-- Hidden MemberID -->
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['MembershipStatus']}</td>
                                            <td>" . number_format($row['Subscription'], 2) . "</td> <!-- Displaying Subscription -->
                                            <td>" . number_format($row['SessionPrice'], 2) . "</td> <!-- Displaying Session Price -->
                                            <td>" . number_format($row['TotalBill'], 2) . "</td> <!-- Displaying Total Bill -->
                                            <td>{$row['Status']}</td> <!-- Displaying Status -->
                                            <td><button class='btn btn-primary openModal' data-memberid='{$row['MemberID']}'>Open Modal</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center'>No members found</td></tr>";
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
                <h5 class="modal-title" id="paymentModalLabel">Payment Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please select an action for this member:</p>
                <button class="btn btn-success" id="payBtn">PAY</button>
                <button class="btn btn-danger" id="cancelBtn">CANCEL</button>
                <button class="btn btn-warning" id="pauseBtn">PAUSE</button>
                <button class="btn btn-info" id="refundBtn">REFUND</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#paymentsTable').DataTable({
            scrollX: true,
            columnDefs: [
                {
                    targets: [0], // Target the first column (MemberID) to hide it
                    visible: false, // Hide the MemberID column
                }
            ]
        });

        // Open modal when "Open Modal" button is clicked
        $('.openModal').click(function () {
            var memberID = $(this).data('memberid');
            $('#paymentModal').data('memberid', memberID).modal('show');
        });

        // Handle different button actions
        $('#payBtn').click(function () {
            var memberID = $('#paymentModal').data('memberid');
            if (confirm('Are you sure you want to process payment for this member?')) {
                $.ajax({
                    url: '../action/payment_process.php',
                    type: 'POST',
                    data: { memberID: memberID, action: 'pay' },
                    success: function (response) {
                        alert(response);
                        $('#paymentModal').modal('hide');
                        location.reload();
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });

        $('#cancelBtn').click(function () {
            var memberID = $('#paymentModal').data('memberid');
            if (confirm('Are you sure you want to cancel payment for this member?')) {
                $.ajax({
                    url: '../action/payment_process.php',
                    type: 'POST',
                    data: { memberID: memberID, action: 'cancel' },
                    success: function (response) {
                        alert(response);
                        $('#paymentModal').modal('hide');
                        location.reload();
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });

        $('#pauseBtn').click(function () {
            var memberID = $('#paymentModal').data('memberid');
            if (confirm('Are you sure you want to pause payment for this member?')) {
                $.ajax({
                    url: '../action/payment_process.php',
                    type: 'POST',
                    data: { memberID: memberID, action: 'pause' },
                    success: function (response) {
                        alert(response);
                        $('#paymentModal').modal('hide');
                        location.reload();
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });

        $('#refundBtn').click(function () {
            var memberID = $('#paymentModal').data('memberid');
            if (confirm('Are you sure you want to refund payment for this member?')) {
                $.ajax({
                    url: '../action/payment_process.php',
                    type: 'POST',
                    data: { memberID: memberID, action: 'refund' },
                    success: function (response) {
                        alert(response);
                        $('#paymentModal').modal('hide');
                        location.reload();
                    },
                    error: function ()