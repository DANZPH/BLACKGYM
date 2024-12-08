<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php'); 
    exit();
}
include '../../database/connection.php';

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

//cards
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

// Daily Earnings (Ensure the timezone is considered)
$dailyEarningsQuery = "SELECT SUM(AmountPaid) AS daily_earnings 
                       FROM Payments 
                       WHERE DATE(CONVERT_TZ(PaymentDate, '+00:00', '+08:00')) = CURDATE()";
$dailyEarningsResult = $conn1->query($dailyEarningsQuery);
$dailyEarnings = $dailyEarningsResult->fetch_assoc()['daily_earnings'] ?? 0;

// Monthly Earnings
$monthlyEarningsQuery = "SELECT SUM(AmountPaid) AS monthly_earnings 
                         FROM Payments 
                         WHERE MONTH(CONVERT_TZ(PaymentDate, '+00:00', '+08:00')) = MONTH(CURDATE()) 
                         AND YEAR(CONVERT_TZ(PaymentDate, '+00:00', '+08:00')) = YEAR(CURDATE())";
$monthlyEarningsResult = $conn1->query($monthlyEarningsQuery);
$monthlyEarnings = $monthlyEarningsResult->fetch_assoc()['monthly_earnings'] ?? 0;

//Chart
// Total Members by Status
$membersQuery = "SELECT MembershipStatus, COUNT(*) AS count FROM Members GROUP BY MembershipStatus";
$membersResult = $conn1->query($membersQuery);
$membersData = [];
while ($row = $membersResult->fetch_assoc()) {
    $membersData[$row['MembershipStatus']] = $row['count'];
}

// Total Payments by Type
$paymentsQuery = "SELECT PaymentType, SUM(Amount) AS total FROM Payments GROUP BY PaymentType";
$paymentsResult = $conn1->query($paymentsQuery);
$paymentsData = [];
while ($row = $paymentsResult->fetch_assoc()) {
    $paymentsData[$row['PaymentType']] = $row['total'];
}

// Attendance Trends
$attendanceQuery = "SELECT DATE(AttendanceDate) AS date, COUNT(*) AS count FROM Attendance GROUP BY DATE(AttendanceDate) LIMIT 7";
$attendanceResult = $conn1->query($attendanceQuery);
$attendanceDates = [];
$attendanceCounts = [];
while ($row = $attendanceResult->fetch_assoc()) {
    $attendanceDates[] = $row['date'];
    $attendanceCounts[] = $row['count'];
}

// Gender Distribution
$genderQuery = "SELECT Gender, COUNT(*) AS count FROM Members GROUP BY Gender";
$genderResult = $conn1->query($genderQuery);
$genderData = [];
while ($row = $genderResult->fetch_assoc()) {
    $genderData[$row['Gender']] = $row['count'];
}

// Count of Subscription and SessionPrice (not total sum)
$membershipQuery = "SELECT COUNT(Subscription) AS subscriptionCount, COUNT(SessionPrice) AS sessionPriceCount FROM Membership WHERE Subscription IS NOT NULL OR SessionPrice IS NOT NULL";
$membershipResult = $conn1->query($membershipQuery);
$membershipCounts = $membershipResult->fetch_assoc();
$subscriptionCount = $membershipCounts['subscriptionCount'];
$sessionPriceCount = $membershipCounts['sessionPriceCount'];
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
                <a href="view_member" class="btn btn-outline-info btn-sm">View</a>
            </div>
        </div>
    </div>
    <!-- Current People Card-->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-dumbbell text-info"></i> Current People
                    </h4>
                    <h2 class="card-text text-info"><?php echo $currentPeople; ?>/50</h2>
                </div>
                <a href="attendance" class="btn btn-outline-info btn-sm">View</a>
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
                <a href="payments" class="btn btn-outline-warning btn-sm">View</a>
            </div>
        </div>
    </div>
    
    <!-- Daily Earnings Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-calendar-day text-info"></i> 
                    Earnings D
                    </h4>
                    <h2 class="card-text text-info">₱<?php echo number_format($dailyEarnings, 2); ?></h2>
                </div>
                <a href="payments" class="btn btn-outline-info btn-sm">View</a>
            </div>
        </div>
    </div>
    <!-- Monthly Earnings Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-calendar-alt text-primary"></i> Earnings M
                    </h4>
                    <h2 class="card-text text-primary">₱<?php echo number_format($monthlyEarnings, 2); ?></h2>
                </div>
                <a href="payments" class="btn btn-outline-primary btn-sm">View</a>
            </div>
        </div>
    </div>
        <!-- Total Payments Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">
                        <i class="fas fa-credit-card text-success"></i> total
                    </h4>
                    <h2 class="card-text text-success">₱<?php echo number_format($totalPayments, 2); ?></h2>
                </div>
                <a href="payments" class="btn btn-outline-success btn-sm">View</a>
            </div>
        </div>
    </div>
</div>
    
    <div class="row mt-4">
            <!-- Chart Section -->
            <div class="row mt-4">
                <!-- Members by Status (Pie Chart) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Status</h4>
                            <canvas id="membersChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Member Type (Pie Chart) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Type</h4>
                            <canvas id="subscriptionSessionPriceChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Gender Distribution (Pie Chart) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Gender</h4>
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Attendance Trends (Line Chart) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Attendance</h4>
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
                  <!-- Payments by Type (Bar Chart) -->
                  <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Payments</h4>
                            <canvas id="paymentsChart"></canvas>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Members by Status (Pie Chart)
        const membersCtx = document.getElementById('membersChart').getContext('2d');
        const membersChart = new Chart(membersCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($membersData)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($membersData)); ?>,
                    backgroundColor: ['#4caf50', '#ff9800', '#f44336'], // Active, Inactive, Suspended
                }]
            }
        });

        // Payments by Type (Bar Chart)
        const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
        const paymentsChart = new Chart(paymentsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($paymentsData)); ?>,
                datasets: [{
                    label: 'Total Payments',
                    data: <?php echo json_encode(array_values($paymentsData)); ?>,
                    backgroundColor: '#2196f3',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Attendance Trends (Line Chart)
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($attendanceDates); ?>,
                datasets: [{
                    label: 'Attendance Count',
                    data: <?php echo json_encode($attendanceCounts); ?>,
                    borderColor: '#ff5722',
                    backgroundColor: 'rgba(255, 87, 34, 0.2)',
                    fill: true,
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Attendance Count'
                        }
                    }
                }
            }
        });

        // Gender Distribution (Pie Chart)
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($genderData)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($genderData)); ?>,
                    backgroundColor: ['#42a5f5', '#f39f9f', '#9c27b0'], // Male, Female, Other
                }]
            }
        });

        // Subscription vs SessionPrice Count (Pie Chart)
        const subscriptionSessionPriceCtx = document.getElementById('subscriptionSessionPriceChart').getContext('2d');
        const subscriptionSessionPriceChart = new Chart(subscriptionSessionPriceCtx, {
            type: 'pie',
            data: {
                labels: ['Subscription', 'Session Price'],
                datasets: [{
                    data: [<?php echo $subscriptionCount; ?>, <?php echo $sessionPriceCount; ?>],
                    backgroundColor: ['#A5C1DC', '#7982B9'],
                }]
            }
        });
    </script>
</body>
</html>
