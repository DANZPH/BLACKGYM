<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Gender Distribution
$genderQuery = "SELECT Gender, COUNT(*) AS count FROM Members GROUP BY Gender";
$genderResult = $conn1->query($genderQuery);
$genderData = [];
while ($row = $genderResult->fetch_assoc()) {
    $genderData[$row['Gender']] = $row['count'];
}

// Total Subscription and SessionPrice
$membershipQuery = "SELECT SUM(Subscription) AS totalSubscription, SUM(SessionPrice) AS totalSessionPrice FROM Membership";
$membershipResult = $conn1->query($membershipQuery);
$membershipTotals = $membershipResult->fetch_assoc();
$totalSubscription = $membershipTotals['totalSubscription'];
$totalSessionPrice = $membershipTotals['totalSessionPrice'];
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
               <!-- Membership Totals -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body text-center">
                            <h4>Total Subscription and SessionPrice</h4>
                            <p><strong>Total Subscription:</strong> <?php echo number_format($totalSubscription, 2); ?></p>
                            <p><strong>Total Session Price:</strong> <?php echo number_format($totalSessionPrice, 2); ?></p>
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
                <div class="col-md-12 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h4 class="text-center">Attendance Trends (Last 7 Days)</h4>
                            <canvas id="attendanceChart"></canvas>
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
      
      // Gender Distribution (Pie Chart)
const genderCtx = document.getElementById('genderChart').getContext('2d');
const genderChart = new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_keys($genderData)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($genderData)); ?>,
            backgroundColor: ['#42a5f5', '#ef5350', '#9c27b0'], // Male, Female, Other
        }]
    }
});
      
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
    </script>
</body>
</html>