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
    <!-- Font Awesome (for icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../../styles.css">
    <style>
        body {
            background-color: #f4f4f4;
            margin-top: 60px; /* Space for fixed navbar */
        }

        .table-responsive {
            overflow-x: auto;
        }

        .card-body {
            padding: 0;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            z-index: 1050;
            transition: 0.3s;
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .content-wrapper {
            margin-left: 250px;
            padding-top: 20px; /* Space for the fixed sidebar */
        }

        .navbar, .sidebar {
            z-index: 1030;
        }

        .content-wrapper {
            margin-left: 250px;
            padding-top: 60px; /* Space for fixed navbar */
        }

        .sidebar-toggler {
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
        }

        /* Hide sidebar on small screens */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 0;
            }
            .sidebar-toggler {
                display: block;
            }
            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

<!-- Include Header -->
<?php include 'includes/header.php'; ?>

<!-- Sidebar with Icon -->
<div class="sidebar">
    <div class="sidebar-toggler" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <!-- Add sidebar menu items -->
    <a href="index.php">Dashboard</a>
    <a href="view_member.php">View Members</a>
    <a href="other_page.php">Other Page</a>
    <!-- Add more links as needed -->
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-9 content-wrapper">
            <h2 class="mb-4">Member List</h2>

            <!-- Card Container for the Table -->
            <div class="card shadow-sm">
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
            scrollX: true, // Enable horizontal scrolling for the DataTable
            order: [[0, 'asc']] // Default sorting by Member ID
        });
    });

    // Toggle Sidebar Function for Mobile View
    function toggleSidebar() {
        var sidebar = document.querySelector('.sidebar');
        sidebar.style.width = sidebar.style.width === '0px' ? '250px' : '0px';
    }
</script>
</body>
</html>