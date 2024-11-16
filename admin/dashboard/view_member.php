<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="includes/styles.css">
    <style>
    /* Sticky Navbar */
.sticky-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999; /* Keep navbar above other content */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Adjust the content so it doesn't overlap with the fixed navbar */
body {
    padding-top: 70px; /* Adjust based on the height of your navbar */
}

/* Adjust Sidebar for Landscape Mode */
.sidebar {
    position: fixed;
    top: 70px; /* Adjust so the sidebar doesn't overlap with the navbar */
    left: 0;
    width: 250px;
    height: calc(100vh - 70px); /* Adjust height to account for the navbar */
    background-color: #2c3e50;
    color: #fff;
    padding-top: 30px;
    z-index: 1000;
    overflow-y: auto;
}

/* Main content should have a margin to the left to avoid overlapping the sidebar */
.main-content {
    margin-left: 250px; /* Sidebar width */
    padding: 20px;
    margin-top: 70px; /* Adjust the top margin for the fixed navbar */
    height: calc(100vh - 70px); /* Adjust height for scrolling */
}

/* Media Query for Landscape Orientation */
@media (orientation: landscape) {
    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px; /* Sidebar width */
        height: calc(100vh - 70px); /* Adjust height to fit the screen */
        background-color: #2c3e50;
        color: #fff;
        padding-top: 30px;
        z-index: 1000;
        overflow-y: auto;
    }

    .main-content {
        margin-left: 260px; /* Account for the sidebar */
    }
}

/* Media Query for Portrait Orientation */
@media (orientation: portrait) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding-top: 0;
        z-index: 0;
    }

    /* Remove the left margin in portrait mode */
    .main-content {
        margin-left: 0;
    }
}
        .table-responsive {
            overflow-x: auto;
        }
        .card-body {
            padding: 0;
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

        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="mb-4">Member List</h2>

            <!-- Card Container for the Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Members Information</h5>
                </div>
                <div class="card-body">
                    <!-- Wrap table in a responsive div -->
                    <div class="table-responsive">
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
                                // Fetch all members and their information
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

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#membersTable').DataTable({
            scrollX: true // Enable horizontal scrolling for the DataTable
        });
    });
</script>
</body>
</html>