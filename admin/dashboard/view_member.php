<?php
// Include the database connection
include '../action/connection.php'; // Adjust the path as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Members List</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Membership Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch members from the database
                $query = "SELECT * FROM Members"; // Replace 'Members' with your actual table name
                $result = $conn1->query($query);

                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$count}</td>
                            <td>{$row['Username']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['Gender']}</td>
                            <td>{$row['Age']}</td>
                            <td>{$row['MembershipStatus']}</td>
                            <td>
                                <button class='btn btn-warning btn-sm' onclick='fetchMember({$row['MemberID']})'>Edit</button>
                                <button class='btn btn-danger btn-sm' onclick='deleteMember({$row['MemberID']})'>Delete</button>
                            </td>
                        </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No members found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Member Modal -->
    <div class="modal fade" id="updateMemberModal" tabindex="-1" role="dialog" aria-labelledby="updateMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateMemberModalLabel">Edit Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateMemberForm">
                    <div class="modal-body">
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
                            <select class="form-control" id="gender" name="gender">
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
                            <label for="membershipStatus">Membership Status</label>
                            <select class="form-control" id="membershipStatus" name="membershipStatus">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fetch Member Details for Edit -->
    <script>
        function fetchMember(memberID) {
            $.ajax({
                type: 'POST',
                url: '../action/edit_member.php', // Replace with the correct path to your edit_member.php
                data: { memberID: memberID },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#memberID').val(data.success.MemberID);
                        $('#username').val(data.success.Username);
                        $('#email').val(data.success.Email);
                        $('#gender').val(data.success.Gender);
                        $('#age').val(data.success.Age);
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

        function deleteMember(memberID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '../action/delete_member.php', // Replace with the correct path to your delete_member.php
                        data: { memberID: memberID },
                        success: function(response) {
                            Swal.fire('Deleted!', 'The member has been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete member.', 'error');
                        }
                    });
                }
            });
        }

        // Handle form submission for updating a member
        $('#updateMemberForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../action/update_member.php', // Replace with the correct path to your update_member.php
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire('Success', 'Member details updated successfully.', 'success').then(() => {
                        $('#updateMemberModal').modal('hide');
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update member details.', 'error');
                }
            });
        });
    </script>
</body>
</html>