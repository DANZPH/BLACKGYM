<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

// Retrieve member's EndDate from the Membership table
$memberID = $_SESSION['MemberID'];
$sql = "SELECT EndDate, Status FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("d", $memberID);
$stmt->execute();
$result = $stmt->get_result();

// Check if membership is found
if ($result->num_rows > 0) {
    $membership = $result->fetch_assoc();
    $endDate = $membership['EndDate'];
    $status = $membership['Status'];

    // Calculate the time left until expiration
    $currentDate = new DateTime();  // Current date and time
    $endDateTime = new DateTime($endDate);  // Membership end date and time
    $interval = $currentDate->diff($endDateTime);  // Difference between current time and membership end time

    // Prepare a human-readable time left message
    $timeLeft = "";
    if ($status == "Active") {
        $timeLeft = $interval->format("%d days, %h hours, %i minutes, %s seconds");
    } else {
        $timeLeft = "Your membership has expired.";
    }

} else {
    $status = "Unknown";
    $timeLeft = "No membership found.";
}

$stmt->close();
$conn1->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Include Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9">
                <h2>Welcome to Your Dashboard</h2>
                <p>Here you can view and manage your membership, payments, and attendance.</p>

                <!-- Membership Status Card -->
                <div class="card">
                    <div class="card-header">
                        Membership Status
                    </div>
                    <div class="card-body">
                        <?php if ($status == "Active"): ?>
                            <h5 class="card-title">Active Membership</h5>
                            <p class="card-text">Your membership is active and valid for the next: <strong><?php echo $timeLeft; ?></strong>.</p>
                        <?php else: ?>
                            <h5 class="card-title">Membership Status: <?php echo $status; ?></h5>
                            <p class="card-text"><?php echo $timeLeft; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Add more content as needed -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>