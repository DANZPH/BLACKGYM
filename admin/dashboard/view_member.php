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
<?php 
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}
include '../../database/connection.php'; 
include '../../includes/head.php';
?>
<body>
<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="col-md-9 content-wrapper">
            <h2 class="mb-4">Member List</h2>
            
            <!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm">
                    <input type="hidden" id="editMemberID" name="memberID">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editGender">Gender</label>
                        <select class="form-control" id="editGender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editAge">Age</label>
                        <input type="number" class="form-control" id="editAge" name="age" required>
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <input type="text" class="form-control" id="editAddress" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="editMembershipStatus">Membership Status</label>
                        <select class="form-control" id="editMembershipStatus" name="membershipStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

            <!-- Add Member Button -->
            <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#addMemberModal">
                Add Member
            </button>

<!--modal add member-->
<?php include 'includes/modal/add_member.php'; ?>
            <!-- Members Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Members Information</h5>
                </div>
                <div class="card-body">
                    <div class="table">
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
                                </tr>
                            </thead>
<tbody>
    <?php
    $sql = "
        SELECT 
            Members.MemberID, 
            Users.Username, 
            Users.Email, 
            Members.Gender, 
            Members.Age, 
            Members.Address, 
            Members.MembershipStatus, 
            Members.created_at 
        FROM Members 
        INNER JOIN Users ON Members.UserID = Users.UserID
    ";
    $result = $conn1->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['MemberID']}</td>
                <td>{$row['Username']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['Gender']}</td>
                <td>{$row['Age']}</td>
                <td>{$row['Address']}</td>
                <td>{$row['MembershipStatus']}</td>
                <td>{$row['created_at']}</td>
                <td><button type='button' class='btn btn-warning edit-btn' data-toggle='modal' data-target='#editMemberModal' data-id='{$row['MemberID']}' data-username='{$row['Username']}' data-email='{$row['Email']}' data-gender='{$row['Gender']}' data-age='{$row['Age']}' data-address='{$row['Address']}' data-status='{$row['MembershipStatus']}'>Edit</button></td>
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

<?php include '../../includes/footer.php'; ?>
<script>
    // Handle Edit button click
    $('.edit-btn').on('click', function() {
        const memberID = $(this).data('id');
        const username = $(this).data('username');
        const email = $(this).data('email');
        const gender = $(this).data('gender');
        const age = $(this).data('age');
        const address = $(this).data('address');
        const status = $(this).data('status');

        // Populate modal fields with current member data
        $('#editMemberID').val(memberID);
        $('#editUsername').val(username);
        $('#editEmail').val(email);
        $('#editGender').val(gender);
        $('#editAge').val(age);
        $('#editAddress').val(address);
        $('#editMembershipStatus').val(status);
    });

    // Handle form submission to save changes
    $('#editMemberForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "../action/edit_member.php", // Change to your process file
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Member updated successfully!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page to show the updated data
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the member. Please try again.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to update member. Please try again later.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>

<!-- JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle membership options
        $('#membershipType').change(function() {
            const type = $(this).val();
            $('#subscriptionOptions').toggle(type === 'Subscription');
            $('#sessionPriceOptions').toggle(type === 'SessionPrice');
        });

        // Initialize DataTable
        $('#membersTable').DataTable({ scrollX: true });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
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
                var otp = Math.floor(100000 + Math.random() * 900000);
                var otpExpiration = new Date(new Date().getTime() + 15 * 60000).toISOString(); 
                
                $.ajax({
                    type: "POST",
                    url: "../action/add_member_process.php",
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
                                    window.location.href = 'otp.php?email=' + $('#email').val();
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
    </script>
</body>
</html>