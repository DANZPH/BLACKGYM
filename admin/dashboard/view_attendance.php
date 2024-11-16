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
    <title>View Members Attendance</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="includes/styles.css">
</head>

<body>

<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="col-md-9">
            <h2 class="mb-4">Member Attendance</h2>

            <div class="card">
                <div class="card-header">
                    <h5>Members Information</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Updated Member Table -->
                        <table id="membersTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Address</th>
                                    <th>Membership Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch all members and their information
                                $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.Gender, Members.Age, 
                                        Members.Address, Members.MembershipStatus, Members.created_at 
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Check if the member has an active attendance record
                                        $attendanceSql = "SELECT AttendanceID, CheckOut, AttendanceCount 
                                                          FROM Attendance WHERE MemberID = ? ORDER BY AttendanceID DESC LIMIT 1";
                                        $stmt = $conn1->prepare($attendanceSql);
                                        $stmt->bind_param("i", $row['MemberID']);
                                        $stmt->execute();
                                        $attendanceResult = $stmt->get_result();
                                        $attendance = $attendanceResult->fetch_assoc();

                                        $buttonText = 'Check In';
                                        $buttonClass = 'btn-success';
                                        if ($attendance && $attendance['CheckOut'] == '0000-00-00 00:00:00') {
                                            $buttonText = 'Check Out';
                                            $buttonClass = 'btn-danger';
                                        }

                                        echo "<tr>
                                            <td>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['Gender']}</td>
                                            <td>{$row['Age']}</td>
                                            <td>{$row['Address']}</td>
                                            <td>{$row['MembershipStatus']}</td>
                                            <td>{$row['created_at']}</td>
                                            <td><button class='btn $buttonClass toggleAttendance' data-memberid='{$row['MemberID']}'>$buttonText</button></td>
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

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables with search and pagination
    $('#membersTable').DataTable();

    // Event listener for toggle attendance button click
    $('.toggleAttendance').click(function() {
        var memberID = $(this).data('memberid');  // Get the member ID from data attribute
        var button = $(this);  // Reference to the button clicked

        $.ajax({
            url: '../action/attendance_process.php',
            type: 'POST',
            data: {
                action: 'toggleAttendance',
                memberID: memberID
            },
            success: function(response) {
                if (response == 'checkedIn') {
                    button.removeClass('btn-success').addClass('btn-danger').text('Check Out');
                } else if (response == 'checkedOut') {
                    button.removeClass('btn-danger').addClass('btn-success').text('Check In');
                } else {
                    alert('An error occurred.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>