<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/login.php');
    exit();
}
include '../../database/connection.php';
include '../../includes/head.php';
?>
<!DOCTYPE html>
<html lang="en">
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
            
            <!-- Modal Edit Member -->
<div class="modal fade" id="updateMemberModal" tabindex="-1" role="dialog" aria-labelledby="updateMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateMemberModalLabel">Edit Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Member Form -->
                <form id="editMemberForm">
                    <input type="hidden" id="memberID" name="memberID">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" id="age" name="age" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="membershipStatus">Membership Status</label>
                        <select class="form-control" id="membershipStatus" name="membershipStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
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

            <!-- Modal Add Member -->
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
                                    <th>Actions</th>
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
                <td>
                    <button class='btn btn-info' onclick='fetchMember({$row['MemberID']})'>Edit</button>
                    <button class='btn btn-danger' onclick='deleteMember({$row['MemberID']})'>Delete</button>
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
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<!-- JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#membersTable').DataTable({ scrollX: true });

    // Handle Add Member form submission via AJAX
    $('#addMemberForm').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: '../action/add_member_process.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
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
                        title: 'Member Added Successfully!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to add member. Please try again later.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

// Function to delete a member
function deleteMember(memberID) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../action/delete_member.php', // Ensure the correct path to the file
                data: { memberID: memberID },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire('Deleted!', data.success, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.error, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete member.', 'error');
                }
            });
        }
    });
}

// Function to fetch member data for editing
function fetchMember(memberID) {
    $.ajax({
        type: 'POST',
        url: '../action/edit_member.php', // Ensure the correct path to the file
        data: { memberID: memberID },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                $('#memberID').val(data.success.MemberID);
                $('#username').val(data.success.Username);
                $('#email').val(data.success.Email);
                $('#gender').val(data.success.Gender);
                $('#age').val(data.success.Age);
                $('#address').val(data.success.Address);
                $('#membershipStatus').val(data.success.MembershipStatus);

                $('#updateMemberModal').modal('show');
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to fetch member details.', 'error');
        }
    });
}

});
</script>
</body>
</html>
