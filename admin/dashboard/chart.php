<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php'); 
    exit();
}
include '../../database/connection.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Fetch monthly earnings for the last 6 months
$lastSixMonthsQuery = "
    SELECT DATE_FORMAT(PaymentDate, '%Y-%m') AS month, SUM(Amount) AS total
    FROM Payments
    WHERE PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(PaymentDate, '%Y-%m')
    ORDER BY DATE_FORMAT(PaymentDate, '%Y-%m')";
$lastSixMonthsResult = $conn1->query($lastSixMonthsQuery);

// Prepare data for the monthly chart
$months = [];
$earnings = [];
while ($row = $lastSixMonthsResult->fetch_assoc()) {
    $months[] = $row['month'];
    $earnings[] = $row['total'];
}

// Fetch daily earnings for the last 1 week
$lastWeekQuery = "
    SELECT DATE(PaymentDate) AS day, SUM(Amount) AS total
    FROM Payments
    WHERE PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(PaymentDate)
    ORDER BY DATE(PaymentDate)";
$lastWeekResult = $conn1->query($lastWeekQuery);

// Prepare data for the daily chart
$days = [];
$dailyEarnings = [];
while ($row = $lastWeekResult->fetch_assoc()) {
    $days[] = $row['day'];
    $dailyEarnings[] = $row['total'];
}

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLACKGYM Dashboard</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- My CSS -->
    <link rel="stylesheet" href="includes/styles.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h1>Hi, <?php echo $_SESSION['username']; ?></h1>
                        <ul class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li><a class="active" href="#">Home</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="row">
                    <!-- Monthly Earnings Chart -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Monthly Earnings (Last 6 Months)</h4>
                                <canvas id="earningsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Earnings Chart -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Daily Earnings (Last 7 Days)</h4>
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Charts -->
                <div class="row">
                    <!-- Status Pie Chart -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Status</h4>
                                <canvas id="membersChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Type Pie Chart -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Type</h4>
                                <canvas id="subscriptionSessionPriceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gender Pie Chart -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Gender</h4>
                                <canvas id="genderChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="row">
                    <!-- Attendance Line Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Attendance Trends</h4>
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Bar Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Payment method</h4>
                                <canvas id="paymentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="includes/JS/sweetalert.js"></script>
    <script src="includes/script.js"></script>
</body>
</html>


    <script>
        // Pass PHP data to JavaScript
        const months = <?php echo json_encode($months); ?>;
        const earnings = <?php echo json_encode($earnings); ?>;
        const days = <?php echo json_encode($days); ?>;
        const dailyEarnings = <?php echo json_encode($dailyEarnings); ?>;

        // Render the monthly chart
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Earnings (₱)',
                    data: earnings,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
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

        // Render the daily chart
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Daily Earnings (₱)',
                    data: dailyEarnings,
                    backgroundColor: 'rgba(153, 102, 255, 0.4)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: true
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
    </script>
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