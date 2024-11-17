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
                                    <th>Option</th> <!-- Dropdown column for actions -->
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
                                            <td>
                                                <select class='custom-select option-select' data-memberid='{$row['MemberID']}'>
                                                    <option value=''>Select Option</option>
                                                    <option value='pay'>PAY</option>
                                                    <option value='cancel'>CANCEL</option>
                                                    <option value='pause'>PAUSE</option>
                                                    <option value='refund'>REFUND</option>
                                                </select>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

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

        $('.option-select').change(function () {
            var memberID = $(this).data('memberid');
            var option = $(this).val(); // Get the selected option value

            if (option) {
                var actionMessage = '';
                switch (option) {
                    case 'pay':
                        actionMessage = 'Are you sure you want to process payment for this member?';
                        break;
                    case 'cancel':
                        actionMessage = 'Are you sure you want to cancel this member?';
                        break;
                    case 'pause':
                        actionMessage = 'Are you sure you want to pause this member\'s membership?';
                        break;
                    case 'refund':
                        actionMessage = 'Are you sure you want to issue a refund for this member?';
                        break;
                    default:
                        actionMessage = '';
                        break;
                }

                if (confirm(actionMessage)) {
                    $.ajax({
                        url: '../action/member_action.php',
                        type: 'POST',
                        data: { memberID: memberID, action: option },
                        success: function (response) {
                            alert(response);
                            location.reload();
                        },
                        error: function () {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            }
        });
    });
</script>

</body>
</html>