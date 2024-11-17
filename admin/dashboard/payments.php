l<?php
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
                                            <td><button class='btn btn-info option-btn' data-memberid='{$row['MemberID']}'>Option</button></td>
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

<!-- Modal for Payment Options -->
<div class="modal fade" id="paymentOptionsModal" tabindex="-1" aria-labelledby="paymentOptionsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentOptionsModalLabel">Select an Action</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <button class="btn btn-success option-action" id="payBtn">PAY</button>
        <button class="btn btn-warning option-action" id="cancelBtn">CANCEL</button>
        <button class="btn btn-primary option-action" id="pauseBtn">PAUSE</button>
        <button class="btn btn-danger option-action" id="refundBtn">REFUND</button>
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
        $('#paymentsTable').DataTable({
            scrollX: true,
            columnDefs: [
                {
                    targets: [0], // Hide MemberID column
                    visible: false,
                }
            ]
        });

        // Open modal when 'Option' button is clicked
        $('.option-btn').click(function () {
            var memberID = $(this).data('memberid');
            $('#paymentOptionsModal').data('memberid', memberID).modal('show');
        });

        // Handle button actions in the modal
        $('.option-action').click(function () {
            var action = $(this).attr('id'); // Get the action (pay, cancel, pause, refund)
            var memberID = $('#paymentOptionsModal').data('memberid');
            var message = '';

            switch (action) {
                case 'payBtn':
                    message = 'Payment processed for Member ID ' + memberID;
                    break;
                case 'cancelBtn':
                    message = 'Payment canceled for Member ID ' + memberID;
                    break;
                case 'pauseBtn':
                    message = 'Membership paused for Member ID ' + memberID;
                    break;
                case 'refundBtn':
                    message = 'Refund issued for Member ID ' + memberID;
                    break;
            }

            // Simulate an action (e.g., make an AJAX request to process the action)
            alert(message);
            $('#paymentOptionsModal').modal('hide');
        });
    });
</script>

</body>
</html>