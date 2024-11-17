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
    <!-- Include SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.4/dist/sweetalert2.min.css" rel="stylesheet">


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.4/dist/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function () {
        $('#payModalForm').submit(function (e) {
            e.preventDefault(); // Prevent default form submission

            // Get form data
            var memberID = $('#memberID').val();
            var paymentType = $('#paymentType').val();
            var amount = $('#amount').val();
            var amountPaid = $('#amountPaid').val();

            // Send the data to payment_process.php
            $.ajax({
                url: 'payment_process.php',
                type: 'POST',
                data: {
                    memberID: memberID,
                    paymentType: paymentType,
                    amount: amount,
                    amountPaid: amountPaid
                },
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Processed',
                            text: response.message,
                        }).then(function() {
                            // Redirect or close the modal
                            location.reload(); // Reload the page or redirect to another page
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Error',
                            text: response.message,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An error occurred while processing the payment. Please try again.',
                    });
                }
            });
        });
    });
</script>


</body>
</html>