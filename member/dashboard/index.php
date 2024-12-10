<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}

// Include database connection
include '../../database/connection.php';

// Fetch the MemberID from the session
$memberID = $_SESSION['MemberID'];

// Fetch membership data
$sql = "SELECT EndDate, Status, Role FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("i", $memberID);
$stmt->execute();
$stmt->bind_result($endDate, $membershipStatus, $userRole);
$stmt->fetch();
$stmt->close();

// Role-based access restriction
if ($userRole !== 'Admin') { // Restrict access to Admin only
    header('Location: ../access_denied.php');
    exit();
}

// Calculate remaining membership time
$remainingTime = "Membership expired or not set.";
if ($endDate) {
    $currentDate = new DateTime();
    $endDateObj = new DateTime($endDate);
    if ($currentDate < $endDateObj) {
        $interval = $currentDate->diff($endDateObj);
        $remainingTime = $interval->format('%m months, %d days remaining');
    } else {
        $membershipStatus = 'Expired';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Portal</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- CSS -->
    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<section id="content">
    <?php include 'includes/header.php'; ?>
    <main>
        <div class="head-title">
            <div class="left">
                <h1>Dashboard</h1>
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard</a></li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li><a class="active" href="#">Home</a></li>
                </ul>
            </div>
        </div>

        <div class="membership-info">
            <h2>Membership Details</h2>
            <p><strong>Status:</strong> <?= htmlspecialchars($membershipStatus) ?></p>
            <p><strong>Remaining Time:</strong> <?= htmlspecialchars($remainingTime) ?></p>
        </div>

        <!-- Admin Functionalities -->
        <div class="admin-tools">
            <h2>Admin Tools</h2>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="manage_memberships.php">Manage Memberships</a></li>
            </ul>
        </div>
    </main>
</section>

<script src="includes/script.js"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../action/logout.php';
            }
        });
    }
</script>
</body>
</html>