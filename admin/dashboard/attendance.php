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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
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
                                    <th>Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT 
                                            Members.MemberID, 
                                            Users.Username, 
                                            Users.Email, 
                                            Members.Gender, 
                                            Members.Age, 
                                            Members.Address, 
                                            Members.MembershipStatus
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Check attendance status
                                        $attendanceSql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' LIMIT 1";
                                        $stmt = $conn1->prepare($attendanceSql);
                                        $stmt->bind_param("i", $row['MemberID']);
                                        $stmt->execute();
                                        $attendanceResult = $stmt->get_result();
                                        $attendance = $attendanceResult->fetch_assoc();

                                        $buttonLabel = 'Check In';
                                        $buttonClass = 'btn-success';
                                        if ($attendance) {
                                            $buttonLabel = 'Check Out';
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
                                            <td><button class='btn $buttonClass' onclick='toggleAttendance({$row['MemberID']})'>$buttonLabel</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>No members found</td></tr>";
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
        // Initialize DataTable
        $('#membersTable').DataTable();

        // Toggle attendance function
        $('.attendance-toggle').on('click', function () {
            const memberID = $(this).data('id'); // Get MemberID from button
            const button = $(this); // Reference the clicked button

            // Send AJAX request
            $.ajax({
                url: '../action/attendance_process.php',
                method: 'POST',
                data: {
                    action: 'toggleAttendance',
                    memberID: memberID
                },
                success: function (response) {
                    if (response === 'checkedIn') {
                        // Update button to reflect Check Out state
                        button.removeClass('btn-success').addClass('btn-danger').text('Check Out');
                        alert('Member checked in.');
                    } else if (response === 'checkedOut') {
                        // Update button to reflect Check In state
                        button.removeClass('btn-danger').addClass('btn-success').text('Check In');
                        alert('Member checked out.');
                    } else {
                        alert('Error: ' + response); // Show specific error
                    }
                },
                error: function () {
                    alert('An error occurred while processing attendance.');
                }
            });
        });
    });
</script>

</body>
</html>