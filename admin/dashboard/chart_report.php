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
            <h1 class="text-center mb-4 text-white">Analytics Dashboard</h1>
            <p class="text-center text-white">Visualize system data with charts below.</p>

            <!-- Chart Section -->
            <div class="row mt-4">
                <!-- Members by Status (Pie Chart) -->
                <div class=".col-sm-1 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Members by Status</h4>
                            <canvas id="membersChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Subscription vs SessionPrice Count (Pie Chart) -->
                <div class=".col-sm-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Subsciption vs Session</h4>
                            <canvas id="subscriptionSessionPriceChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Attendance Trends (Line Chart) -->
                <div class="col-md-12 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Attendance Trends (Last 7 Days)</h4>
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gender Distribution (Pie Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Gender</h4>
                            <canvas id="genderChart"></canvas>
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