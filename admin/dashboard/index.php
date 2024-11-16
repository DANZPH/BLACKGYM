l<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // If admin is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Include database connection
include '../../database/connection.php';

// Fetch statistics from the database
$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM Members WHERE MembershipStatus = 'Active'";
$totalMembersResult = $conn1->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

$totalPaymentsQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalPaymentsResult = $conn1->query($totalPaymentsQuery);
$totalPayments = $totalPaymentsResult->fetch_assoc()['total_amount'];

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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
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
        /* Base styles (default) */
body {
    font-family: Arial, sans-serif;
}

/* Large devices (desktops, screens wider than 1200px) */
@media (min-width: 1200px) {
    .sidebar {
        width: 250px;
        height: 100%;
        padding-top: 20px;
    }

    .content-wrapper {
        margin-left: 250px;
    }
}

/* Medium devices (tablets, screens between 768px and 1199px) */
@media (min-width: 768px) and (max-width: 1199px) {
    .sidebar {
        width: 200px;
        height: 100%;
        padding-top: 20px;
    }

    .content-wrapper {
        margin-left: 200px;
    }

    /* Adjust the sidebar menu items for better readability on medium devices */
    .sidebar .nav-item {
        font-size: 14px;
    }
}

/* Small devices (mobile, screens below 768px) */
@media (max-width: 767px) {
    /* Make the sidebar full-width and stack vertically */
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding-top: 0;
        background-color: #343a40;
    }

    .content-wrapper {
        margin-left: 0;
        padding-top: 60px; /* Adjust for the sticky navbar */
    }

    /* For mobile view, navbar should be sticky at the top */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
    }

    /* Stack sidebar items vertically for mobile */
    .sidebar .nav-item {
        font-size: 16px;
        text-align: left;
        padding-left: 10px;
    }

    /* Adjust card sizes for mobile view */
    .card {
        margin-bottom: 20px;
    }

    .monitor-card .card-body {
        display: block;
    }
}

/* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575px) {
    /* Adjust content wrapper for smaller phones */
    .content-wrapper {
        margin-left: 0;
        padding-top: 60px; /* Adjust for the sticky navbar */
    }

    .sidebar {
        display: none; /* Hide sidebar on very small devices */
    }

    /* Ensure the navbar items are stacked and easy to tap on */
    .navbar-nav {
        text-align: center;
    }
    
    .navbar-toggler {
        display: block;
    }

    /* Card responsiveness for very small screens */
    .card {
        width: 100%;
    }
}
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <!-- Include Header -->
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
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

        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>