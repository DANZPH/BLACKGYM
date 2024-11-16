<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Total Active Members</h4>
                                <h2><?php echo $totalMembers; ?></h2>
                            </div>
                            <a href="members.php" class="btn btn-info btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Total Payments</h4>
                                <h2>₱<?php echo number_format($totalPayments, 2); ?></h2>
                            </div>
                            <a href="payments.php" class="btn btn-info btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Pending Memberships</h4>
                                <h2><?php echo $pendingPayments; ?></h2>
                            </div>
                            <a href="pending_memberships.php" class="btn btn-warning btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More content can go here -->
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>