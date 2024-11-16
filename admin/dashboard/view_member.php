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
    <!-- Universal CSS -->
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <!-- Header -->
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <div class="container-fluid mt-3">
                <h2 class="mb-4">Member List</h2>

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
        </main>
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
            $('#membersTable').DataTable(); // Initialize DataTable
        });
    </script>
</body>
</html>