<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Total Members with Active Membership Status
$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM Members WHERE MembershipStatus = 'Active'";
$totalMembersResult = $conn1->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

// Total Payments
$totalPaymentsQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalPaymentsResult = $conn1->query($totalPaymentsQuery);
$totalPayments = $totalPaymentsResult->fetch_assoc()['total_amount'];

// Total Pending Payments (Payments related to Pending Membership)
$pendingPaymentsQuery = "SELECT COUNT(*) AS Status FROM Membership WHERE Status = 'Pending'";
$pendingPaymentsResult = $conn1->query($pendingPaymentsQuery);
$pendingPayments = $pendingPaymentsResult->fetch_assoc()['Status'];

// Current People at the Gym
$currentPeopleQuery = "SELECT COUNT(*) AS current_people FROM Attendance WHERE CheckOut IS NULL OR CheckOut = '0000-00-00 00:00:00'";
$currentPeopleResult = $conn1->query($currentPeopleQuery);
$currentPeople = $currentPeopleResult->fetch_assoc()['current_people'];
?>
<!DOCTYPE html>
<html lang="en">

<?php include '../../includes/head.php'; ?>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
            <h1 class="text-center mb-4 text-white">DASHBOARD</h1>
            <p class="text-center text-white">Monitor and manage system activities below.</p>

            <!-- Dashboard Cards -->
<div class="row mt-4">
    <!-- Active Members Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-users text-primary"></i> Active Members
                    </h4>
                    <h2 class="card-text text-primary"><?php echo $totalMembers; ?></h2>
                </div>
                <a href="members.php" class="btn btn-outline-info btn-sm">View</a>
            </div>
        </div>
    </div>

    <!-- Total Payments Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-credit-card text-success"></i> Earnings
                    </h4>
                    <h2 class="card-text text-success">₱<?php echo number_format($totalPayments, 2); ?></h2>
                </div>
                <a href="payments.php" class="btn btn-outline-success btn-sm">View</a>
            </div>
        </div>
    </div>

    <!-- Pending Memberships Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-clock text-warning"></i> Pending
                    </h4>
                    <h2 class="card-text text-warning"><?php echo $pendingPayments; ?></h2>
                </div>
                <a href="pending_memberships.php" class="btn btn-outline-warning btn-sm">View</a>
            </div>
        </div>
    </div>

    <!-- Current People at the Gym Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-dumbbell text-info"></i> Current People
                    </h4>
                    <h2 class="card-text text-info"><?php echo $currentPeople; ?>/50</h2>
                </div>
                <a href="attendance.php" class="btn btn-outline-info btn-sm">View</a>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
                    <?php include '../../includes/footer.php'; ?>


    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
