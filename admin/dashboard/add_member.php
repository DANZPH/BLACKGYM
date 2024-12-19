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
                            <a class="active" href="#">Members</a>
                        </li>
                    </ul>
                </div>
                <a href="../action/fetch_receipt.php" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Print</span>
                </a>
            </div>
                <!-- Add Member Modal -->
                <?php include 'includes/modal/add_member.php'; ?>
                                <?php include 'includes/modal/edit_member.php'; ?>
 
                <!-- Members Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Members Information</h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#addMemberModal">
                            <i class="fas fa-user-plus"></i> Add New
                        </button>
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
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.Gender, Members.Age, 
                                            Members.Address, Members.MembershipStatus, Members.created_at 
                                            FROM Members 
                                            INNER JOIN Users ON Members.UserID = Users.UserID";
                                    $result = $conn1->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td class='d-none'>{$row['MemberID']}</td>
                                                <td>{$row['Username']}</td>
                                                <td>{$row['Email']}</td>
                                                <td>{$row['Gender']}</td>
                                                <td>{$row['Age']}</td>
                                                <td>{$row['Address']}</td>
                                                <td>{$row['MembershipStatus']}</td>
                                                <td>{$row['created_at']}</td>
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
                                        echo "<tr><td colspan='9' class='text-center'>No members found</td></tr>";
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
$(document).ready(function(){
            // Toggle Subscription and SessionPrice options based on membership type
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

            // Submit form via AJAX
            $('#registerForm').submit(function(e){
                e.preventDefault();

                // Generate a random OTP for the user
                var otp = Math.floor(100000 + Math.random() * 900000);
                var otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString();  // OTP expires in 15 minutes
                
                $.ajax({
                    type: "POST",
                    url: "../../login/send_otp.php",
                    data: {
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: $('#password').val(),
                        gender: $('#gender').val(),
                        age: $('#age').val(),
                        address: $('#address').val(),
                        membershipType: $('#membershipType').val(),
                        subscriptionMonths: $('#subscriptionMonths').val(),
                        sessionPrice: $('#sessionPrice').val(),
                        otp: otp,
                        otpExpiration: otpExpiration
                    },
                    success: function(response){
                        if (response.trim() === "Email already registered.") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Email already registered.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful!',
                                text: 'Verification OTP sent to your email.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '../../login/otp.php?email=' + $('#email').val();
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to send OTP. Please try again later.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
/*Update member*/
$(document).ready(function() {
// Initialize DataTable
$('#membersTable').DataTable();

 $(document).ready(function() {
    // Edit button click event
    $('.editBtn').click(function() {
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
                $('#editAge').val(data.Age);
                $('#editAddress').val(data.Address);
                $('#editMembershipStatus').val(data.MembershipStatus);
                $('#editMemberForm').attr('action', '../action/edit_member.php?MemberID=' + memberID);
                $('#editMemberModal').modal('show');
            }
        });
    });

    // Delete button click event
    $('.deleteBtn').click(function() {
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


    });
    </script>
</body>
</html>