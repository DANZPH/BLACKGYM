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
                  <?php include '../../includes/head.php'; ?>

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
                <?php include '../../includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>