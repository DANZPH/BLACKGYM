<?php
include '../../database/connection.php';

// Fetch data for charts
// 1. Gender Distribution
$genderData = [];
$genderQuery = $conn->query("SELECT Gender, COUNT(*) AS Count FROM Members GROUP BY Gender");
while ($row = $genderQuery->fetch_assoc()) {
    $genderData[] = $row;
}

// 2. Membership Status Distribution
$membershipStatusData = [];
$membershipStatusQuery = $conn->query("SELECT MembershipStatus, COUNT(*) AS Count FROM Members GROUP BY MembershipStatus");
while ($row = $membershipStatusQuery->fetch_assoc()) {
    $membershipStatusData[] = $row;
}

// 3. Daily Attendance
$attendanceData = [];
$attendanceQuery = $conn->query("SELECT DATE(AttendanceDate) AS Date, COUNT(*) AS AttendanceCount FROM Attendance GROUP BY DATE(AttendanceDate)");
while ($row = $attendanceQuery->fetch_assoc()) {
    $attendanceData[] = $row;
}

// 4. Payment Type Distribution
$paymentTypeData = [];
$paymentTypeQuery = $conn->query("SELECT PaymentType, SUM(AmountPaid) AS TotalAmount FROM Payments GROUP BY PaymentType");
while ($row = $paymentTypeQuery->fetch_assoc()) {
    $paymentTypeData[] = $row;
}

// 5. Daily Revenue
$dailyRevenueData = [];
$dailyRevenueQuery = $conn->query("SELECT DATE(PaymentDate) AS Date, SUM(AmountPaid) AS TotalRevenue FROM Payments GROUP BY DATE(PaymentDate)");
while ($row = $dailyRevenueQuery->fetch_assoc()) {
    $dailyRevenueData[] = $row;
}

// 6. Active Membership Distribution by Start Date Range
$membershipRangeData = [];
$membershipRangeQuery = $conn->query("
    SELECT 
        CASE 
            WHEN StartDate >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN 'Last 30 Days'
            WHEN StartDate >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN 'Last 3 Months'
            ELSE 'Older' 
        END AS DateRange, 
        COUNT(*) AS Count 
    FROM Membership 
    WHERE Status = 'Active' 
    GROUP BY DateRange
");
while ($row = $membershipRangeQuery->fetch_assoc()) {
    $membershipRangeData[] = $row;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Analytics Dashboard</title>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS CDN for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold text-center mb-10">Enhanced Analytics Dashboard</h1>

    <!-- Gender Distribution Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Gender Distribution</h2>
        <canvas id="genderChart"></canvas>
    </div>

    <!-- Membership Status Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Membership Status Distribution</h2>
        <canvas id="membershipStatusChart"></canvas>
    </div>

    <!-- Daily Attendance Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Daily Attendance</h2>
        <canvas id="attendanceChart"></canvas>
    </div>

    <!-- Payment Type Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Payment Type Distribution</h2>
        <canvas id="paymentTypeChart"></canvas>
    </div>

    <!-- Daily Revenue Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Daily Revenue</h2>
        <canvas id="dailyRevenueChart"></canvas>
    </div>

    <!-- Active Membership Distribution Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Active Membership Distribution</h2>
        <canvas id="membershipRangeChart"></canvas>
    </div>
</div>

<script>
    // Gender Distribution Chart Data
    const genderLabels = <?= json_encode(array_column($genderData, 'Gender')) ?>;
    const genderCounts = <?= json_encode(array_column($genderData, 'Count')) ?>;

    const genderChartCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderChartCtx, {
        type: 'pie',
        data: {
            labels: genderLabels,
            datasets: [{
                label: 'Gender Distribution',
                data: genderCounts,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                hoverOffset: 4
            }]
        }
    });

    // Membership Status Chart Data
    const membershipLabels = <?= json_encode(array_column($membershipStatusData, 'MembershipStatus')) ?>;
    const membershipCounts = <?= json_encode(array_column($membershipStatusData, 'Count')) ?>;

    const membershipChartCtx = document.getElementById('membershipStatusChart').getContext('2d');
    new Chart(membershipChartCtx, {
        type: 'bar',
        data: {
            labels: membershipLabels,
            datasets: [{
                label: 'Membership Status',
                data: membershipCounts,
                backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
            }],
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        }
    });

    // Daily Attendance Chart Data
    const attendanceLabels = <?= json_encode(array_column($attendanceData, 'Date')) ?>;
    const attendanceCounts = <?= json_encode(array_column($attendanceData, 'AttendanceCount')) ?>;

    const attendanceChartCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceChartCtx, {
        type: 'line',
        data: {
            labels: attendanceLabels,
            datasets: [{
                label: 'Daily Attendance',
                data: attendanceCounts,
                borderColor: '#2196F3',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Payment Type Chart Data
    const paymentLabels = <?= json_encode(array_column($paymentTypeData, 'PaymentType')) ?>;
    const paymentAmounts = <?= json_encode(array_column($paymentTypeData, 'TotalAmount')) ?>;

    const paymentChartCtx = document.getElementById('paymentTypeChart').getContext('2d');
    new Chart(paymentChartCtx, {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                label: 'Payments by Type',
                data: paymentAmounts,
                backgroundColor: ['#FFC107', '#03A9F4', '#8BC34A'],
                hoverOffset: 4
            }]
        }
    });

    // Daily Revenue Chart Data
    const revenueLabels = <?= json_encode(array_column($dailyRevenueData, 'Date')) ?>;
    const revenueAmounts = <?= json_encode(array_column($dailyRevenueData, 'TotalRevenue')) ?>;

    const revenueChartCtx = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(revenueChartCtx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Daily Revenue',
                data: revenueAmounts,
                borderColor: '#FF5722',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Active Membership Distribution Chart Data
    const membershipRangeLabels = <?= json_encode(array_column($membershipRangeData, 'DateRange')) ?>;
    const membershipRangeCounts = <?= json_encode(array_column($membershipRangeData, 'Count')) ?>;

    const membershipRangeChartCtx = document.getElementById('membershipRangeChart').getContext('2d');
    new Chart(membershipRangeChartCtx, {
        type: 'bar',
        data: {
            labels: membershipRangeLabels,
            datasets: [{
                label: 'Active Memberships',
                data: membershipRangeCounts,
                backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>