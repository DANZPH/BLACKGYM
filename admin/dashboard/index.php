<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // If admin is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Include database connection
include '../../database/connection.php';

// Fetch statistics from the database

// Total Members with Active Membership Status
$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM Members WHERE MembershipStatus = 'Active'";
$totalMembersResult = $conn1->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

// Total Payments
$totalPaymentsQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalPaymentsResult = $conn1->query($totalPaymentsQuery);
$totalPayments = $totalPaymentsResult->fetch_assoc()['total_amount'];

// Total Pending Payments (Payments related to Pending Membership)
$pendingPaymentsQuery = "SELECT COUNT(*) AS pending_payments FROM Membership WHERE Status = 'Pending'";
$pendingPaymentsResult = $conn1->query($pendingPaymentsQuery);
$pendingPayments = $pendingPaymentsResult->fetch_assoc()['pending_payments'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="includes/styles.css">
<!--    <style>
        /* Sidebar Customization */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
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
            padding: 20px;
        }
        .card {
            border-radius: 10px;
        }
        .monitor-card {
            background-color: #f8f9fa;
        }
        .monitor-card .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>--> 
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <!-- Include Header -->
        <?php include 'includes/header.php'; ?>

        <div class="container col-md-9">
            <h2>Welcome to the Admin Dashboard</h2>
            <p>Monitor and manage system activities below.</p>

            <!-- Monitoring Section -->
            <div class="row mt-4">
                <!-- Total Members with Active Status -->
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-header">
                            <h4>Total Active Members</h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <h2><?php echo $totalMembers; ?></h2>

                            </div>
                            <div>
                                <a href="members.php" class="btn btn-info btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Payments -->
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-header">
                            <h4>Total Payments</h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <h2>₱<?php echo number_format($totalPayments, 2); ?></h2>
                            </div>
                            <div>
                                <a href="payments.php" class="btn btn-info btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments (Based on Pending Membership Status) -->
                <div class="col-md-4">
                    <div class="card monitor-card shadow-sm">
                        <div class="card-header">
                            <h4>Pending Memberships</h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <h2><?php echo $pendingPayments; ?></h2>
                            </div>
                            <div>
                                <a href="pending_memberships.php" class="btn btn-warning btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Content Can Go Here -->
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>