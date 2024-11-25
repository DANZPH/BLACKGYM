<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>

<style>
    /* Ensure the modal appears above other content */
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    .content-wrapper {
        position: relative;
        z-index: 1; /* Ensure lower than modal */
    }

    /* Adjust header position if fixed */
    nav.navbar {
        z-index: 1030; /* Ensure modal and backdrop appear above the navbar */
    }
</style>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid mt-3">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <div class="col-md-9 content-wrapper">
                <h2 class="mb-4">Member History</h2>
                <div class="card">
                    <div class="card-header">
                        <h5>Member Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="historyTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="display:none;">Member ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Membership Status</th>
                                        <th>Total Bill</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.MembershipStatus, 
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
                                                <td style='display:none;'>{$row['MemberID']}</td>
                                                <td>{$row['Username']}</td>
                                                <td>{$row['Email']}</td>
                                                <td>{$row['MembershipStatus']}</td>
                                                <td>" . number_format($row['TotalBill'], 2) . "</td>
                                                <td>{$row['Status']}</td>
                                                <td><button class='btn btn-primary history-btn' data-memberid='{$row['MemberID']}'>View History</button></td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No members found</td></tr>";
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

    <!-- Modal for History -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Payment History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="historyContent">Loading...</div>
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
            $('#historyTable').DataTable({
                scrollX: true,
                columnDefs: [
                    {
                        targets: [0], // Target the first column (MemberID) to hide it
                        visible: false, // Hide the MemberID column
                    }
                ]
            });

            $('.history-btn').click(function () {
                var memberID = $(this).data('memberid');
                $('#historyContent').html('Loading...');
                $('#historyModal').modal('show');

                $.ajax({
                    url: '../action/history_process.php',
                    type: 'GET',
                    data: { MemberID: memberID },
                    success: function (response) {
                        $('#historyContent').html(response); // Update modal with history details
                    },
                    error: function () {
                        $('#historyContent').html('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>