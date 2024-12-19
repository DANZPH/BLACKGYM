<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php'); 
    exit();
}
include '../../database/connection.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
// Cards
// Fetch available trainers from the Staff table (assuming 'Trainer' is a JobTitle)
$trainersSql = "SELECT COUNT(*) FROM Staff WHERE JobTitle = 'Trainer' AND NOT EXISTS (
                    SELECT 1 FROM StaffPresence WHERE StaffPresence.StaffID = Staff.StaffID AND StaffPresence.Status = 'Absent'
                )";
$trainersStmt = $conn1->prepare($trainersSql);
$trainersStmt->execute();
$trainersStmt->bind_result($availableTrainers);
$trainersStmt->fetch();
$trainersStmt->close();

// Total Members with Active Membership Status
$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM Members WHERE MembershipStatus = 'Active'";
$totalMembersResult = $conn1->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

// Total Payments
$totalPaymentsQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalPaymentsResult = $conn1->query($totalPaymentsQuery);
$totalPayments = $totalPaymentsResult->fetch_assoc()['total_amount'] ?? 0;

// Total Borrow Balance (sum of negative balances)
$totalBorrowBalanceQuery = "SELECT SUM(Balance) AS borrow_balance FROM Members WHERE Balance < 0";
$totalBorrowBalanceResult = $conn1->query($totalBorrowBalanceQuery);
$totalBorrowBalance = $totalBorrowBalanceResult->fetch_assoc()['borrow_balance'] ?? 0;

// Adjusted Total Payments (subtracting Borrow Balance)
$adjustedTotalPayments = $totalPayments - abs($totalBorrowBalance); // abs() to ensure the borrow balance is a positive value

// Total Pending Payments
$pendingPaymentsQuery = "SELECT COUNT(*) AS Status FROM Membership WHERE Status = 'Pending'";
$pendingPaymentsResult = $conn1->query($pendingPaymentsQuery);
$pendingPayments = $pendingPaymentsResult->fetch_assoc()['Status'];

// Current People at the Gym
$currentPeopleQuery = "SELECT COUNT(*) AS current_people FROM Attendance WHERE CheckOut IS NULL OR CheckOut = '0000-00-00 00:00:00'";
$currentPeopleResult = $conn1->query($currentPeopleQuery);
$currentPeople = $currentPeopleResult->fetch_assoc()['current_people'];

// Total Available Balance (sum of positive balances)
$totalAvailableBalanceQuery = "SELECT SUM(Balance) AS available_balance FROM Members WHERE Balance > 0";
$totalAvailableBalanceResult = $conn1->query($totalAvailableBalanceQuery);
$totalAvailableBalance = $totalAvailableBalanceResult->fetch_assoc()['available_balance'] ?? 0;

// Daily Payments (for today)
$today = date('Y-m-d');
$dailyEarningsQuery = "SELECT SUM(Amount) AS daily_earnings FROM Payments WHERE DATE(PaymentDate) = '$today'";
$dailyEarningsResult = $conn1->query($dailyEarningsQuery);
$dailyEarnings = $dailyEarningsResult->fetch_assoc()['daily_earnings'] ?? 0;

// Monthly Payments (for current month)
$currentMonth = date('Y-m');
$monthlyEarningsQuery = "SELECT SUM(Amount) AS monthly_earnings FROM Payments WHERE DATE_FORMAT(PaymentDate, '%Y-%m') = '$currentMonth'";
$monthlyEarningsResult = $conn1->query($monthlyEarningsQuery);
$monthlyEarnings = $monthlyEarningsResult->fetch_assoc()['monthly_earnings'] ?? 0;

// Chart data
// Members by Status
$membersQuery = "SELECT MembershipStatus, COUNT(*) AS count FROM Members GROUP BY MembershipStatus";
$membersResult = $conn1->query($membersQuery);
$membersData = [];
while ($row = $membersResult->fetch_assoc()) {
    $membersData[$row['MembershipStatus']] = $row['count'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLACKGYM Dashboard</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Hi, <?php echo $_SESSION['username']; ?></h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Home</a></li>
                    </ul>
                </div>
            </div>

            <ul class="box-info">
                <li>
                    <i class='bx'>₱</i>
                    <span class="text">
                        <h3>₱<?php echo number_format($monthlyEarnings, 2); ?></h3> <!-- Monthly Earnings -->
                        <p>This Month's</p>
                    </span>
                </li>
                <li>
                    <i class='bx'>₱</i>
                    <span class="text">
                        <h3>₱<?php echo number_format($adjustedTotalPayments, 2); ?></h3> <!-- Displaying the adjusted total payments -->
                        <p>Total Earnings</p>
                    </span>
                </li>
                <li>
                    <i class='bx'>₱</i>
                    <span class="text">
                        <h3>₱<?php echo number_format($dailyEarnings, 2); ?></h3> <!-- Daily Earnings -->
                        <p>Today's Earnings</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-group'></i>
                    <span class="text">
                        <h3><?php echo $totalMembers; ?></h3>
                        <p>Active Members</p>
                    </span>
                </li>                
                <li>
                    <i class='bx bx-dumbbell'></i>
                    <span class="text">
                        <h3><?php echo $availableTrainers; ?></h3>
                        <p>Available Trainers</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-user-check'></i>
                    <span class="text">
                        <h3><?php echo $currentPeople; ?></h3>
                        <p>At Gym</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-wallet'></i>
                    <span class="text">
                        <h3>₱<?php echo number_format($totalAvailableBalance, 2); ?></h3>
                        <p>Total Reserves</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-money'></i>
                    <span class="text">
                        <h3>₱<?php echo number_format($totalBorrowBalance, 2); ?></h3>
                        <p>Aggregate Loans</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-hourglass'></i>
                    <span class="text">
                        <h3><?php echo $pendingPayments; ?></h3>
                        <p>Pending</p>
                    </span>
                </li>
            </ul>

        </main>
    </section>
    <script src="includes/JS/sweetalert.js"></script>
    <script src="includes/script.js"></script>
</body>
</html>
