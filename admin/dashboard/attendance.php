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
<?php include '../../includes/head.php';?>

<body>

<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 content-wrapper">
            <h2 class="mb-4">Member Attendance</h2>

            <!-- Card Container for the Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Members Attendance Information</h5>
                </div>
                <div class="card-body">
                    <!-- Wrap table in a responsive div -->
                    <div class="table-responsive">
                        <table id="membersTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
<!--                                    <th>Member ID
Members.MemberID, 

                                            <td>{$row['MemberID']}</td>
                                
</th>-->
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Attendance Count</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch members with attendance details
                                $sql = "
                                    SELECT 
                                        
                                        Users.Username, 
                                        Users.Email, 
                                        Attendance.AttendanceCount, 
                                        Attendance.CheckIn, 
                                        Attendance.CheckOut 
                                    FROM Members 
                                    INNER JOIN Users ON Members.UserID = Users.UserID 
                                    LEFT JOIN Attendance ON Members.MemberID = Attendance.MemberID 
                                    ORDER BY Attendance.CheckIn DESC
                                ";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $buttonText = 'Check In';
                                        $buttonClass = 'btn-success';
                                        if ($row['CheckOut'] === '0000-00-00 00:00:00' || $row['CheckOut'] === null) {
                                            $buttonText = 'Check Out';
                                            $buttonClass = 'btn-danger';
                                        }

                                        echo "<tr>
            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['AttendanceCount']}</td>
                                            <td>{$row['CheckIn']}</td>
                                            <td>{$row['CheckOut']}</td>
                                            <td>
                                                <button class='btn $buttonClass toggleAttendance' data-memberid='{$row['MemberID']}'>$buttonText</button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No attendance records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <?php include '../../includes/footer.php'; ?>
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
    $(document).ready(function () {
        $('#membersTable').DataTable({
            scrollX: true // Enable horizontal scrolling for the DataTable
        });

        // Event listener for toggle attendance button click
        $('.toggleAttendance').click(function () {
            var memberID = $(this).data('memberid');  // Get the member ID from the data attribute
            var button = $(this);  // Reference to the button clicked

            $.ajax({
                url: '../action/attendance_process.php',
                type: 'POST',
                data: {
                    action: 'toggleAttendance',
                    memberID: memberID
                },
                success: function (response) {
                    if (response === 'checkedIn') {
                        button.removeClass('btn-success').addClass('btn-danger').text('Check Out');
                    } else if (response === 'checkedOut') {
                        button.removeClass('btn-danger').addClass('btn-success').text('Check In');
                    } else {
                        alert('An error occurred.');
                    }
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