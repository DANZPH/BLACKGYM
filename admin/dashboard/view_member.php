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
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="col-md-9 p-0" style="margin-left: 20%;">
            <h2 class="mb-4">Member List</h2>

            <!-- Add Member Modal -->
            <?php include 'includes/modal/add_member.php'; ?>

            <!-- Members Table -->
            <table id="membersTable" class="table table-striped table-bordered" style="width: 100%;">
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
                                    <button class='btn btn-warning btn-sm editBtn mx-2' data-id='{$row['MemberID']}'>
                                        <i class='fas fa-edit'></i> 
                                    </button>
                                    <button class='btn btn-danger btn-sm deleteBtn mx-2' data-id='{$row['MemberID']}'>
                                        <i class='fas fa-trash'></i> 
                                    </button>
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

<?php include '../../includes/footer.php'; ?>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#membersTable').DataTable({
        "responsive": true,
        "autoWidth": false
    });
});
</script>
</body>
</html>