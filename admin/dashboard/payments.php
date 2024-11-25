<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>

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
                                    <th style="display:none;">Member ID</th>
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
                                        Membership.Status
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID
                                        LEFT JOIN Membership ON Members.MemberID = Membership.MemberID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td style='display:none;'>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['MembershipStatus']}</td>
                                            <td>" . number_format($row['Subscription'], 2) . "</td>
                                            <td>" . number_format($row['SessionPrice'], 2) . "</td>
                                            <td>" . number_format($row['TotalBill'], 2) . "</td>
                                            <td>{$row['Status']}</td>
                                            <td>
                                                <button class='btn btn-primary pay-btn' data-memberid='{$row['MemberID']}'
                                                data-totalbill='{$row['TotalBill']}' data-email='{$row['Email']}'>Pay</button>
                                            </td>
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

<?php include '../../includes/footer.php'; ?>

<!-- Payment Modal -->
<div class="modal" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Process Payment</h5>
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
          <input type="hidden" id="email" name="email">
          <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function () {
        $('#paymentsTable').DataTable({
            scrollX: true
        });

        $('.pay-btn').click(function () {
            var memberID = $(this).data('memberid');
            var totalBill = $(this).data('totalbill');
            var email = $(this).data('email');

            $('#memberID').val(memberID);
            $('#email').val(email);
            $('#amount').val(totalBill);
            $('#paymentModal').modal('show');
        });

        $('#amountPaid').on('input', function () {
            var amount = parseFloat($('#amount').val());
            var amountPaid = parseFloat($(this).val());
            var change = amountPaid - amount;
            $('#change').val(change.toFixed(2));
        });

        $('#paymentForm').submit(function (e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '../action/payment_process.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    alert(response);
                    $('#paymentModal').modal('hide');
                    location.reload();
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
</body>
</html>