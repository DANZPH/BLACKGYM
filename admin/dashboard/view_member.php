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
                        <li><a class="active" href="#">Members</a></li>
                    </ul>
                </div><button type="button" class="btn-download" data-toggle="modal" data-target="#addMemberModal">
            <i class="fas fa-user-plus"></i> Add New
        </button>

            </div>
            <!-- Add Member Modal -->
            <?php include 'includes/modal/add_member.php'; ?>
            <?php include 'includes/modal/edit_member.php'; ?>
<!-- Members Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="membersTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="d-none">MemberID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Membership Status</th>
                        <th>Balance</th>
                        <th>Created At</th>
                        <th>Membership Info</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.Gender, Members.Age, 
                            Members.Address, Members.MembershipStatus, Members.Balance, Members.created_at,
                            Membership.EndDate
                            FROM Members 
                            INNER JOIN Users ON Members.UserID = Users.UserID
                            LEFT JOIN Membership ON Membership.MemberID = Members.MemberID";
                    $result = $conn1->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Calculate remaining time
                            if ($row['EndDate']) {
                                $currentDate = new DateTime(); // Current date and time
                                $endDateObj = new DateTime($row['EndDate']); // Convert EndDate to DateTime object
                                $interval = $currentDate->diff($endDateObj); // Calculate the difference

                                // Format remaining time as months and days
                                $remainingTime = $interval->format('%m months, %d days');
                                $endDateFormatted = date('d M Y', strtotime($row['EndDate']));
                            } else {
                                $remainingTime = "No expiration date set."; // Fallback if no EndDate found
                                $endDateFormatted = "N/A"; // Show 'N/A' if EndDate is not available
                            }

                            // Add color coding for Membership Status
                            $membershipStatusClass = ($row['MembershipStatus'] == 'Active') ? 'text-success' : 'text-danger';
                            $membershipStatusLabel = ($row['MembershipStatus'] == 'Active') ? 'Active' : 'Inactive';
                            
                            // Set Membership Info as empty for inactive members
                            $membershipInfo = ($row['MembershipStatus'] == 'Active') ? "<p><strong>Until: </strong><small>{$endDateFormatted}</small></p>
                                                                                  <p><strong>Ends: </strong><small>{$remainingTime}</small></p>" : "";

                            echo "<tr>
                                <td class='d-none'>{$row['MemberID']}</td>
                                <td>{$row['Username']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Gender']}</td>
                                <td>{$row['Age']}</td>
                                <td>{$row['Address']}</td>
                                <td class='{$membershipStatusClass}'><strong>{$membershipStatusLabel}</strong></td>
                                <td>{$row['Balance']}</td>
                                <td>{$row['created_at']}</td>
                                <td>{$membershipInfo}</td>
                                <td>
                                    <div class='d-flex'>
                                        <button class='btn btn-warning btn-sm editBtn mx-2' data-id='{$row['MemberID']}'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button class='btn btn-danger btn-sm deleteBtn mx-2' data-id='{$row['MemberID']}'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-center'>No members found</td></tr>";
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
/*add member*/
$(document).ready(function() {
                $('#membershipType').change(function() {
                var membershipType = $(this).val();
                if (membershipType === 'Subscription') {
                    $('#subscriptionOptions').show();
                    $('#sessionPriceOptions').hide();
                } else {
                    $('#sessionPriceOptions').show();
                    $('#subscriptionOptions').hide();
                }
            });
    $('#registerForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "../action/send_reset_token.php", // Adjust the path as needed
            data: {
                username: $('#username').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                gender: $('#gender').val(),
                age: $('#age').val(),
                address: $('#address').val(),
                membershipType: $('#membershipType').val(),
                subscriptionMonths: $('#subscriptionMonths').val(),
                sessionPrice: $('#sessionPrice').val()
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
    var table = $('#membersTable').DataTable({
        responsive: true,  // Make the table responsive for smaller screens
        pageLength: 10,    // Set the default page length to 10 rows
        lengthMenu: [10, 25, 50, 100],  // Option to choose rows per page
        columnDefs: [
            {
                targets: [0],  // Hides the MemberID column (index 0)
                visible: false
            }
        ]
    });

    // Event delegation for Edit button click event
    $(document).on('click', '.editBtn', function() {
        var memberID = $(this).data('id');
        $.ajax({
            url: '../action/fetch_member.php',
            method: 'GET',
            data: { memberID: memberID },
            success: function(response) {
                var data = JSON.parse(response);
                $('#editUsername').val(data.Username);
                $('#editEmail').val(data.Email);
                $('#editGender').val(data.Gender);
                $('#editBalance').val(data.Balance); // Set Balance in modal
                $('#editAge').val(data.Age);
                $('#editAddress').val(data.Address);
                $('#editMembershipStatus').val(data.MembershipStatus);
                $('#editMemberForm').attr('action', '../action/edit_member.php?MemberID=' + memberID);
                $('#editMemberModal').modal('show'); // Ensure modal is triggered
            }
        });
    });

    // Event delegation for Delete button click event
    $(document).on('click', '.deleteBtn', function() {
        var memberID = $(this).data('id');
        
        // SweetAlert confirmation for deletion
        Swal.fire({
            title: 'Are you sure?',
            text: "You will not be able to recover this member!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action if confirmed
                window.location.href = '../action/delete_member.php?MemberID=' + memberID;
            }
        });
    });
});
    </script>
</body>
</html>
