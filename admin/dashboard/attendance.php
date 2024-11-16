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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Attendance</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
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
            <h2 class="mb-4">Member Attendance</h2>

            <!-- Card Container for the Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Attendance Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Username</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Attendance Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "
                                    SELECT 
                                        Members.MemberID, 
                                        Users.Username, 
                                        Attendance.CheckIn, 
                                        Attendance.CheckOut, 
                                        Attendance.AttendanceCount
                                    FROM Members
                                    LEFT JOIN Users ON Members.UserID = Users.UserID
                                    LEFT JOIN Attendance ON Members.MemberID = Attendance.MemberID
                                    ORDER BY Attendance.AttendanceDate DESC
                                ";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr id='member-{$row['MemberID']}'>
                                            <td>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>" . ($row['CheckIn'] ? $row['CheckIn'] : 'N/A') . "</td>
                                            <td>" . ($row['CheckOut'] ? $row['CheckOut'] : 'N/A') . "</td>
                                            <td>{$row['AttendanceCount']}</td>
                                            <td><button class='btn btn-".($row['CheckOut'] ? 'danger' : 'success')." attendance-toggle' data-memberid='{$row['MemberID']}'>
                                                ".($row['CheckOut'] ? 'Check Out' : 'Check In')."</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No attendance records found</td></tr>";
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
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        scrollX: true // Enable horizontal scrolling for the DataTable
    });

    // Toggle Check In/Check Out
    $('.attendance-toggle').click(function() {
        var memberID = $(this).data('memberid');
        var button = $(this);

        alert("Clicked MemberID: " + memberID); // Debugging: show the clicked member ID

        $.ajax({
            url: 'attendance_process.php',
            type: 'POST',
            data: { action: 'toggleAttendance', memberID: memberID },
            success: function(response) {
                alert("Server Response: " + response); // Debugging: show server response
                
                if (response === 'checkedIn') {
                    button.removeClass('btn-success').addClass('btn-danger').text('Check Out');
                } else if (response === 'checkedOut') {
                    button.removeClass('btn-danger').addClass('btn-success').text('Check In');
                }
            },
            error: function(xhr, status, error) {
                alert("AJAX error: " + status + ": " + error); // Show any AJAX errors
            }
        });
    });
});</script>
</body>
</html>