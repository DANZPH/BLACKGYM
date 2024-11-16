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
    <title>Attendance</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            width: 250px;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .navbar {
            padding: 0.75rem 1rem;
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>

<body>

<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="mb-4">Attendance</h2>

            <!-- Card Container for the Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Members Attendance</h5>
                </div>
                <div class="card-body">
                    <!-- Wrap table in a responsive div -->
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch all members and their information
                                $sql = "SELECT MemberID, Username FROM Members";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>
                                                <button class='btn btn-primary attendance-btn' data-memberid='{$row['MemberID']}' data-action='Check In'>Check In</button>
                                                <button class='btn btn-danger attendance-btn' data-memberid='{$row['MemberID']}' data-action='Check Out'>Check Out</button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>No members found</td></tr>";
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

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable();

        // Handle Check In / Check Out button clicks
        $(document).on('click', '.attendance-btn', function () {
            const memberId = $(this).data('memberid');
            const action = $(this).data('action');
            
            $.post('action/attendance_process.php', { memberId, action }, function (response) {
                alert(response.message);
                location.reload(); // Reload the page to reflect changes
            }, 'json');
        });
    });
</script>
</body>
</html>