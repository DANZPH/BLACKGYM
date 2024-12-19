<?php
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

    <style>
        /* Limit table width and add horizontal scrolling */
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
            white-space: nowrap;
        }

        /* Apply ellipsis for long content */
        td {
            max-width: 150px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Customize the DataTable */
        table.dataTable {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 10px;
        }
    </style>
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
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Staff</a></li>
                    </ul>
                </div>
                <a href="../action/fetch_receipt.php" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Print</span>
                </a>
            </div>
            <!-- Add Staff Modal -->
            <?php include 'includes/modal/add_staff.php'; ?>
            <?php include 'includes/modal/edit_staff.php'; ?>
            <?php include 'includes/modal/manage_attendance.php'; ?> 
            <!-- Staff Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Staff Information</h5>
                </div>
                <div class="card-body">
                     <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#addStaffModal">
                        <i class="fas fa-user-plus"></i> Add Staff
                    </button>
                    <div class="table-responsive">
                        <table id="staffTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="d-none">StaffID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Job Title</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT Staff.StaffID, Users.Username, Users.Email, Staff.JobTitle, Staff.created_at
                                        FROM Staff 
                                        INNER JOIN Users ON Staff.UserID = Users.UserID";
                                $result = $conn1->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td class='d-none'>{$row['StaffID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['JobTitle']}</td>
                                            <td>{$row['created_at']}</td>
                                            <td>
    <div class='d-flex'>
        <button class='btn btn-warning btn-sm editBtn mx-2' data-id='{$row['StaffID']}'>
            <i class='fas fa-edit'></i>
        </button>
        <button class='btn btn-danger btn-sm deleteBtn mx-2' data-id='{$row['StaffID']}'>
            <i class='fas fa-trash'></i>
        </button>
        <!-- Manage Attendance Button -->
        <button class='btn btn-success btn-sm mx-2 manageAttendanceBtn' data-id='{$row['StaffID']}'>
            <i class='fas fa-calendar-check'></i>
        </button>
    </div>
</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No staff found</td></tr>";
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
    <script src="includes/script.js"></script>
    <!-- JavaScript Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="includes/JS/sweetalert.js"></script>

    <!-- Custom JS -->
    
    <script>
$(document).ready(function() {
    // Handle Manage Attendance button click
    $('.manageAttendanceBtn').click(function() {
        var staffID = $(this).data('id');
        $('#manageAttendanceModal').modal('show');
        
        // Set the staff ID in the modal (You could pass other staff details if needed)
        $('#saveAttendanceBtn').data('staffid', staffID);
    });

    // Handle Save Attendance Button click
    $('#saveAttendanceBtn').click(function() {
        var staffID = $(this).data('staffid');
        var date = $('#attendanceDate').val();
        var status = $('#attendanceStatus').val();

        if (date === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a date.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }

        $.ajax({
            type: "POST",
            url: "../action/manage_attendance.php", // Adjust the path as needed
            data: {
                staffID: staffID,
                attendanceDate: date,
                status: status
            },
            success: function(response) {
                if (response.trim() === "Attendance recorded successfully.") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    $('#manageAttendanceModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while managing attendance.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
$(document).ready(function() {
    $('#registerStaffForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "../action/add_staff.php", // Adjust the path as needed
            data: {
                username: $('#username').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                jobTitle: $('#jobTitle').val()
            },
            success: function(response) {
                if (response.trim() === "Email already registered.") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Email already registered.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                } else if (response.trim() === "Verification link sent.") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: 'Verification link sent to your email. Please check your inbox to verify your account.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to send verification link. Please try again later.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
$(document).ready(function() {
    // Initialize DataTable with options
    var table = $('#staffTable').DataTable({
        responsive: true,  // Make the table responsive for smaller screens
        pageLength: 10,    // Set the default page length to 10 rows
        lengthMenu: [10, 25, 50, 100],  // Option to choose rows per page
        columnDefs: [
            {
                targets: [0],  // Hides the StaffID column (index 0)
                visible: false
            }
        ]
    });

    // Event delegation for Edit button click event
    $(document).on('click', '.editBtn', function() {
        var staffID = $(this).data('id');
        $.ajax({
            url: '../action/fetch_staff.php',
            method: 'GET',
            data: { staffID: staffID },
            success: function(response) {
                var data = JSON.parse(response);
                $('#editUsername').val(data.Username);
                $('#editEmail').val(data.Email);
                $('#editJobTitle').val(data.JobTitle); // Set JobTitle in modal
                $('#editStaffForm').attr('action', '../action/edit_staff.php?StaffID=' + staffID);
                $('#editStaffModal').modal('show'); // Ensure modal is triggered
            }
        });
    });

    // Event delegation for Delete button click event
    $(document).on('click', '.deleteBtn', function() {
        var staffID = $(this).data('id');
        
        // SweetAlert confirmation for deletion
        Swal.fire({
            title: 'Are you sure?',
            text: "You will not be able to recover this staff member!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action if confirmed
                window.location.href = '../action/delete_staff.php?StaffID=' + staffID;
            }
        });
    });

    // Event delegation for Manage Attendance button click event
    $(document).on('click', '.manageAttendanceBtn', function() {
        var staffID = $(this).data('id');
        $('#manageAttendanceModal').modal('show');
        
        // Set the staff ID in the modal (You could pass other staff details if needed)
        $('#saveAttendanceBtn').data('staffid', staffID);
    });

    // Handle Save Attendance Button click
    $('#saveAttendanceBtn').click(function() {
        var staffID = $(this).data('staffid');
        var date = $('#attendanceDate').val();
        var status = $('#attendanceStatus').val();

        if (date === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a date.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }

        $.ajax({
            type: "POST",
            url: "../action/manage_attendance.php", // Adjust the path as needed
            data: {
                staffID: staffID,
                attendanceDate: date,
                status: status
            },
            success: function(response) {
                if (response.trim() === "Attendance recorded successfully.") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    $('#manageAttendanceModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while managing attendance.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle Add Staff Form Submission (for registering new staff)
    $('#registerStaffForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "../action/add_staff.php", // Adjust the path as needed
            data: {
                username: $('#username').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                jobTitle: $('#jobTitle').val()
            },
            success: function(response) {
                if (response.trim() === "Email already registered.") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Email already registered.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                } else if (response.trim() === "Verification link sent.") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: 'Verification link sent to your email. Please check your inbox to verify your account.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to send verification link. Please try again later.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
    </script>
</body>
</html>