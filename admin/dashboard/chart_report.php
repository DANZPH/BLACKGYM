<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Total Members by Status
$membersQuery = "SELECT MembershipStatus, COUNT(*) AS count FROM Members GROUP BY MembershipStatus";
$membersResult = $conn1->query($membersQuery);
$membersData = [];
while ($row = $membersResult->fetch_assoc()) {
    $membersData[$row['MembershipStatus']] = $row['count'];
}

// Total Payments by Type
$paymentsQuery = "SELECT PaymentType, SUM(AmountPaid) AS total FROM Payments GROUP BY PaymentType";
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

// Total Revenue
$totalRevenueQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalRevenueResult = $conn1->query($totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['totalRevenue'];

// Revenue by Payment Type
$revenueByTypeQuery = "SELECT PaymentType, SUM(AmountPaid) AS revenue FROM Payments GROUP BY PaymentType";
$revenueByTypeResult = $conn1->query($revenueByTypeQuery);
$revenueByTypeData = [];
while ($row = $revenueByTypeResult->fetch_assoc()) {
    $revenueByTypeData[$row['PaymentType']] = $row['revenue'];
}

// Revenue Trends (Last 7 Days)
$revenueTrendsQuery = "
    SELECT DATE(PaymentDate) AS date, SUM(AmountPaid) AS dailyRevenue 
    FROM Payments 
    WHERE PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    GROUP BY DATE(PaymentDate)";
$revenueTrendsResult = $conn1->query($revenueTrendsQuery);
$revenueDates = [];
$revenueValues = [];
while ($row = $revenueTrendsResult->fetch_assoc()) {
    $revenueDates[] = $row['date'];
    $revenueValues[] = $row['dailyRevenue'];
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include '../../includes/head.php'; ?>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
            <h1 class="text-center mb-4 text-white">Analytics Dashboard</h1>
            <p class="text-center text-white">Visualize system data with charts below.</p>

            <!-- Chart Section -->
            <div class="row mt-4">
                <!-- Members by Status (Pie Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Members by Status</h4>
                            <canvas id="membersChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Payments by Type (Bar Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Payments by Type</h4>
                            <canvas id="paymentsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Attendance Trends (Line Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Attendance Trends (Last 7 Days)</h4>
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue Analytics -->
                <div class="row mt-4">
                    <!-- Total Revenue (Text Display) -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body text-center">
                                <h4>Total Revenue</h4>
                                <h2 class="text-success">
                                    <?php echo number_format($totalRevenue, 2); ?> USD
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue by Payment Type (Pie Chart) -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Revenue by Payment Type</h4>
                                <canvas id="revenueTypeChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Trends (Line Chart) -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0">
                            <div class="card-body">
                                <h4 class="text-center">Revenue Trends (Last 7 Days)</h4>
                                <canvas id="revenueTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <!-- JavaScript -->
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
                    backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
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
                    y: { beginAtZero: true }
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
            }
        });

        // Revenue by Payment Type (Pie Chart)
        const revenueTypeCtx = document.getElementById('revenueTypeChart').getContext('2d');
        const revenueTypeChart = new Chart(revenueTypeCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($revenueByTypeData)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($revenueByTypeData)); ?>,
                    backgroundColor: ['#4caf50', '#ff9800', '#2196f3'],
                }]
            }
        });

        // Revenue Trends (Line Chart)
        const revenueTrendsCtx = document.getElementById('revenueTrendsChart').getContext('2d');
        const revenueTrendsChart = new Chart(revenueTrendsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($revenueDates); ?>,
                datasets: [{
                    label: 'Daily Revenue',
                    data: <?php echo json_encode($revenueValues); ?>,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    fill: true,
                }]
            }
        });
    </script>
</body>
</html>