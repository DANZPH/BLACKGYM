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
include '../../includes/head.php';
?>
<body>
<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<div class="container-fluid p-0">
    <div class="row no-gutters">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="col-md-9 ml-sm-auto col-lg-10 px-4" style="min-height: 100vh; display: flex; flex-direction: column;">
            <div class="card flex-grow-1 d-flex" style="margin-top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Members Information</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <!-- Add Member Button -->
                    <button type="button" class="btn btn-primary mb-4 align-self-start" data-toggle="modal" data-target="#addMemberModal">
                        <i class="fas fa-user-plus"></i> Add new
                    </button>

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
                                <form id="editMemberForm" action="../action/edit_member.php" method="POST">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="editUsername">Username</label>
                                            <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editEmail">Email</label>
                                            <input type="email" class="form-control" id="editEmail" name="editEmail" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="editGender">Gender</label>
                                            <select class="form-control" id="editGender" name="editGender" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="editAge">Age</label>
                                            <input type="number" class="form-control" id="editAge" name="editAge" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editAddress">Address</label>
                                            <input type="text" class="form-control" id="editAddress" name="editAddress" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editMembershipStatus">Membership Status</label>
                                            <select class="form-control" id="editMembershipStatus" name="editMembershipStatus" required>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive flex-grow-1">
                        <table id="membersTable" class="table table-striped table-bordered w-100">
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
                                            <td class='d-none'>{$row['MemberID']}</td>
                                            <td>{$row['Username']}</td>
                                            <td>{$row['Email']}</td>
                                            <td>{$row['Gender']}</td>
                                            <td>{$row['Age']}</td>
                                            <td>{$row['Address']}</td>
                                            <td>{$row['MembershipStatus']}</td>
                                            <td>{$row['created_at']}</td>
                                            <td>
                                                <div class='d-flex gap-2'>
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
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script src="includes/JS/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</body>
</html>