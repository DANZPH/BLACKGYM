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
                                    <th>Subscription</th>
                                    <th>Session Price</th>
                                    <th>Total Bill</th>
                                    <th>Status</th>
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
                                            <td>" . number_format($row['Subscription'], 2) . "</td>
                                            <td>" . number_format($row['SessionPrice'], 2) . "</td>
                                            <td>" . number_format($row['TotalBill'], 2) . "</td>
                                            <td>{$row['Status']}</td>
                                            <td><button class='btn btn-primary pay-btn' data-memberid='{$row['MemberID']}' 
                                                data-totalbill='{$row['TotalBill']}'>Pay</button></td>
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

<!-- Modal for Payment -->
<div class="modal" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
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
            <select class="form-control" id="paymentType" name="paymentType">
              <option value="Cash">Cash</option>
              <option value="Credit">Credit</option>
              <option value="Debit">Debit</option>
            </select>
          </div>
          <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" readonly>
          </div>
          <div class="form-group">
            <label for="amountPaid">Amount Paid</label>
            <input type="number" class="form-control" id="amountPaid" name="amountPaid" required>
          </div>
          <div class="form-group">
            <label for="change">Change</label>
            <input type="number" class="form-control" id="change" name="change" readonly>
          </div>
          <input type="hidden" id="memberID" name="memberID">
          <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#paymentsTable').DataTable({
            scrollX: true,
            columnDefs: [
                {
                    targets: [0], // Target the first column (MemberID) to hide it
                    visible: false, // Hide the MemberID column
                }
            ]
        });

        // On clicking the 'Pay' button, show the payment modal
        $('.pay-btn').click(function () {
            var memberID = $(this).data('memberid');
            var totalBill = $(this).data('totalbill');
            
            // Set the member ID and total bill in the form
            $('#memberID').val(memberID);
            $('#amount').val(totalBill); // Set the amount to the total bill
            $('#paymentModal').modal('show'); // Show the modal
        });

        // Calculate the change when the user enters an amount paid
        $('#amountPaid').on('input', function () {
            var amount = parseFloat($('#amount').val());
            var amountPaid = parseFloat($(this).val());
            var change = amountPaid - amount;
            $('#change').val(change.toFixed(2)); // Show the change
        });

        // On form submission, send the payment data to the backend
        $('#paymentForm').submit(function (e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Get all form data

            $.ajax({
                url: '../action/payment_process.php', // Backend PHP file
                type: 'POST', // Use POST request
                data: formData, // Send form data
                dataType: 'json', // Expect a JSON response
                success: function (response) {
                    // Check the response status
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful',
                            text: response.message
                        }).then(() => {
                            $('#paymentModal').modal('hide'); // Hide the modal after success
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An error occurred. Please try again.'
                    });
                }
            });
        });
    });
</script>

</body>
</html>