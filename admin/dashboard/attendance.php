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
                            <a class="active" href="#">Attendance</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Members Attendance Information</h5>
                </div>
                <div class="card-body">
                    <!-- Wrap table in a responsive div -->
                    <div class="table">
                        <table id="membersTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Attendance Count</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Duration</th> <!-- New column for Duration -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch members with attendance details, now including MemberID and Duration
                                $sql = "
                                    SELECT 
                                        Members.MemberID, 
                                        Users.Username, 
                                        Users.Email, 
                                        Attendance.AttendanceCount, 
                                        Attendance.CheckIn, 
                                        Attendance.CheckOut, 
                                        SEC_TO_TIME(TIMESTAMPDIFF(SECOND, Attendance.CheckIn, Attendance.CheckOut)) AS Duration
                                    FROM Members 
                                    LEFT JOIN Users ON Members.UserID = Users.UserID 
                                    LEFT JOIN Attendance ON Members.MemberID = Attendance.MemberID 
                                    ORDER BY Attendance.CheckIn DESC
                                ";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Determine button status based on check-in/check-out state
                                        $buttonText = 'Check In';
                                        $buttonClass = 'btn-success';
                                        if ($row['CheckOut'] !== '0000-00-00 00:00:00' && $row['CheckOut'] !== null) {
                                            // Member is checked out, allow for Check In
                                            $buttonText = 'Check In';
                                            $buttonClass = 'btn-success';
                                        } elseif ($row['CheckOut'] === '0000-00-00 00:00:00' || $row['CheckOut'] === null) {
                                            // Member is checked in, allow for Check Out
                                            $buttonText = 'Check Out';
                                            $buttonClass = 'btn-danger';
                                        }

                                        // If no attendance record exists, treat as Check In
                                        if ($row['MemberID'] && !$row['CheckIn'] && !$row['CheckOut']) {
                                            $buttonText = 'Check In';
                                            $buttonClass = 'btn-success'; // Green "Check In" button
                                        }

                                        echo "<tr>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['AttendanceCount']}</td>
                                            <td>{$row['CheckIn']}</td>
                                            <td>{$row['CheckOut']}</td>
                                            <td>{$row['Duration']}</td> <!-- Display calculated duration -->
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
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

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
        scrollX: true, // Enable horizontal scrolling
        lengthMenu: [10, 25, 50, 100], // Set the options for number of entries to show
        pageLength: 10 // Default number of entries per page
    });

    // Event listener for toggle attendance button click
    $('#membersTable').on('click', '.toggleAttendance', function () {
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
                location.reload();
                if (response === 'checkedIn') {
                    button.removeClass('btn-success').addClass('btn-danger').text('Check Out');
                } else if (response === 'checkedOut') {
                    button.removeClass('btn-danger').addClass('btn-success').text('Check In');                    
                } else if (response === 'error') {
                    alert('An error occurred while processing attendance.');
                } else if (response === 'noRecord') {
                    alert('No attendance record found for this member.');
                } else {
                    alert(response);
                }
            },
            error: function () {
                // alert('An error occurred. Please try again.');
            }
        });
    });
});
    </script>
    <script src="includes/JS/sweetalert.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="includes/script.js"></script>
</body>
</html>