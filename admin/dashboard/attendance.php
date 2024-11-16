
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['AdminID'])) {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit();
}
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles.css">
    <style>
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
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #575757;
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

            <div class="col-md-9">
                <h2 class="mb-4">Attendance</h2>
                <div class="card">
                    <div class="card-header">
                        <h5>Check-In/Check-Out Members</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT Members.MemberID, Users.Username 
                                        FROM Members 
                                        INNER JOIN Users ON Members.UserID = Users.UserID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Check current attendance status for today
                                        $attendanceCheck = "SELECT CheckOut, AttendanceCount FROM Attendance 
                                                            WHERE MemberID = {$row['MemberID']} 
                                                            AND DATE(AttendanceDate) = CURDATE()";
                                        $attendanceResult = $conn1->query($attendanceCheck);
                                        $status = "Not Checked In";
                                        $action = "Check In";

                                        if ($attendanceResult->num_rows > 0) {
                                            $attendanceData = $attendanceResult->fetch_assoc();
                                            if ($attendanceData['CheckOut'] == '0000-00-00 00:00:00') {
                                                $status = "Checked In";
                                                $action = "Check Out";
                                            } else {
                                                $status = "Checked Out";
                                                $action = "Completed";
                                            }
                                        }

                                        echo "<tr>
                                            <td>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>{$status}</td>
                                            <td>
                                                <button 
                                                    class='btn btn-primary attendance-btn' 
                                                    data-memberid='{$row['MemberID']}' 
                                                    data-action='{$action}'" . ($action === "Completed" ? "disabled" : "") . ">
                                                    {$action}
                                                </button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No members found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).on('click', '.attendance-btn', function () {
            const memberId = $(this).data('memberid');
            const action = $(this).data('action');
            if (action !== 'Completed') {
                $.post('action/attendance_process.php', { memberId, action }, function (response) {
                    alert(response.message);
                    location.reload();
                }, 'json');
            }
        });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).on('click', '.attendance-btn', function () {
        const memberId = $(this).data('memberid');
        const action = $(this).data('action');

        console.log("Button clicked for member: " + memberId + " with action: " + action); // Debugging log

        // Make sure the action is valid
        if (action !== 'Completed') {
            $.post('action/attendance_process.php', { memberId, action }, function (response) {
                console.log("Response from server: ", response); // Debugging log
                
                if (response.message) {
                    alert(response.message);  // Display message from backend
                }
                location.reload();  // Reload page to reflect changes
            }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error in request: ", textStatus, errorThrown); // Debugging log
                alert("There was an error processing your request.");
            });
        }
    });
</script>
</body>
</html>