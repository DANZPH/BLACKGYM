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
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            width: 250px;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .navbar {
            padding: 0.75rem 1rem;
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .monitor-card {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-nav .nav-link {
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>
        
        <div class="container mt-5">
            <h2>Welcome to Admin Dashboard</h2>
            <p>Monitor and manage system activities below.</p>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-body">
                            <h4>View All Members</h4>
                            <table id="memberTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Member ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Example query to fetch member data
                                    $sql = "SELECT m.MemberID, u.Username, u.Email, m.Gender, m.Age, m.MembershipStatus
                                            FROM Members m
                                            JOIN Users u ON m.UserID = u.UserID";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>" . $row['MemberID'] . "</td>
                                                    <td>" . $row['Username'] . "</td>
                                                    <td>" . $row['Email'] . "</td>
                                                    <td>" . $row['Gender'] . "</td>
                                                    <td>" . $row['Age'] . "</td>
                                                    <td>" . $row['MembershipStatus'] . "</td>
                                                    <td><a href='edit_member.php?id=" . $row['MemberID'] . "' class='btn btn-warning btn-sm'>Edit</a></td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No members found</td></tr>";
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#memberTable').DataTable({
                "searching": true, // Enable search functionality
                "ordering": true,  // Enable sorting
                "paging": true,    // Enable pagination
                "lengthMenu": [10, 25, 50, 100] // Options for number of entries per page
            });
        });
    </script>
</body>
</html>