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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../../img/favicon-512x512.png" sizes="512x512" type="image/png">
        <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <!-- CONTENT -->
    <section id="content">
        <?php include 'includes/navbar.php'; ?>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Hi, <?php echo $_SESSION['username']; ?></h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
<!--main-->
                            <div class="card">
                    <div class="card-header">
                        <h5>Member Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table">
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
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
        <!-- Modal for History -->
<?php include 'includes/modal/payment_history.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $('#historyTable').DataTable({
        scrollX: true,  // Enable horizontal scrolling
        columnDefs: [
            {
                targets: [0],  // Hide MemberID column
                visible: false
            }
        ]
    });

    // Use event delegation for dynamically generated elements (history-btn)
    $(document).on('click', '.history-btn', function () {
        var memberID = $(this).data('memberid');  // Get the MemberID
        $('#historyContent').html('Loading...');
        $('#historyModal').modal('show');  // Show the modal

        // Make an AJAX request to fetch the history data for the selected member
        $.ajax({
            url: '../action/history_process.php',  // Endpoint to fetch history
            type: 'GET',
            data: { MemberID: memberID },
            success: function (response) {
                $('#historyContent').html(response);  // Update modal with the fetched history details
            },
            error: function () {
                $('#historyContent').html('An error occurred. Please try again.');  // Handle errors
            }
        });
    });
});
    </script>
    <script src="includes/script.js"></script>
</body>
</html>

