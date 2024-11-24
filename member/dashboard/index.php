<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';
$memberID = $_SESSION['MemberID'];
$sql = "SELECT EndDate, Status FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("d", $memberID);
$stmt->execute();
$stmt->bind_result($endDate, $membershipStatus);
$stmt->fetch();
$stmt->close();

// Check if a valid EndDate exists
if ($endDate) {
    $currentDate = new DateTime(); // Current date and time
    $endDateObj = new DateTime($endDate); // Convert EndDate to DateTime object
    $interval = $currentDate->diff($endDateObj); // Calculate the difference between the current date and the EndDate

    // Display remaining time in months and days format
    $remainingTime = $interval->format('%m months, %d days'); // Months and Days format
} else {
    $remainingTime = "No expiration date set."; // Fallback if no EndDate found
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    html, body {
        background: linear-gradient(90deg, #bdc3c7, #2c3e50);
        height: 100%;
        margin: 0;
        font-family: 'Roboto', sans-serif;
    }

    /* Fix the header */
    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background-color: #fff; /* Optional: Background color for the header */
    }

    /* Ensure the body starts below the header */
    .content-container {
        margin-top: 70px; /* Adjust this value based on the height of the header */
    }

    .membership-card {
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
    }
    .membership-card h5 {
        font-weight: bold;
    }
    .membership-card .status-active {
        color: green;
        font-weight: bold;
    }
    .membership-card .status-expired {
        color: red;
        font-weight: bold;
    }
    .remaining-time {
        font-size: 1.2em;
        margin-top: 10px;
    }
    .remaining-time span {
        font-weight: bold;
        color: #007bff;
    }
    </style>
</head>
<body>
    <header>
        <?php include 'includes/header.php'; ?>
    </header>

    <div class="container content-container">
        <div class="row">

            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9">
                <h2>Hi!, 
                <?php echo $_SESSION['username']; ?>
                </h2>
                <p>Here you can view and manage your BLACKGYM membership, payments, and attendance.</p>

                <!-- Membership Status Section -->
                <?php if ($membershipStatus !== 'Pending'): ?>
                    <div class="card membership-card">
                        <div class="card-header">
                            <h5>Membership Status</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($endDate): ?>
                                <h5 class="card-title status-active">Active Membership</h5>
                                <p class="card-text">Your membership is valid until <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
                                <div class="remaining-time">
                                    <p>Time remaining: <span><?php echo $remainingTime; ?></span></p>
                                </div>
                            <?php else: ?>
                                <h5 class="card-title status-expired">No Active Membership</h5>
                                <p class="card-text">It seems you don't have an active membership at the moment. Please renew or contact support.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Your membership is still pending. Please PAY and wait for approval.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <!-- <?php include '../../includes/footer.php'; ?> -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>