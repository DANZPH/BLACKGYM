<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Members</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
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
            <h2>Member List</h2>
            <p>View and manage all members below.</p>

            <!-- DataTable -->
            <table id="membersTable" class="display table table-bordered">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Username</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming you have a database connection setup and query to fetch members
                    include 'includes/db_connection.php';

                    $sql = "SELECT * FROM Members INNER JOIN Users ON Members.UserID = Users.UserID";
                    $result = mysqli_query($conn, $sql);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['MemberID']}</td>
                                <td>{$row['Username']}</td>
                                <td>{$row['Gender']}</td>
                                <td>{$row['Age']}</td>
                                <td>{$row['Address']}</td>
                                <td>{$row['MembershipStatus']}</td>
                                <td>
                                    <a href='view_member.php?id={$row['MemberID']}' class='btn btn-info btn-sm'>View</a>
                                    <a href='edit_member.php?id={$row['MemberID']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_member.php?id={$row['MemberID']}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery and DataTables JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#membersTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [5, 10, 25, 50]
            });
        });
    </script>
</body>
</html>