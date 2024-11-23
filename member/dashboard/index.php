<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

// Get the current MemberID
$memberID = $_SESSION['MemberID'];

// Fetch the membership details including the EndDate
$query = "SELECT Status, EndDate FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($query);
$stmt->bind_param("d", $memberID);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($status, $endDate);

// Check if a row is returned
if ($stmt->fetch()) {
    $membershipStatus = $status;
    $membershipEndDate = $endDate;
    
    // Calculate the time remaining until expiration
    $currentTime = new DateTime(); // Current time
    $endDateTime = new DateTime($membershipEndDate); // End date from the database
    $interval = $currentTime->diff($endDateTime); // Get the difference

    // Format the remaining time in a readable format (e.g., 1 day, 3 hours)
    $remainingTime = '';
    if ($interval->days > 0) {
        $remainingTime .= $interval->days . ' days ';
    }
    if ($interval->h > 0) {
        $remainingTime .= $interval->h . ' hours ';
    }
    if ($interval->i > 0) {
        $remainingTime .= $interval->i . ' minutes';
    }

    // If the membership has expired
    if ($currentTime >= $endDateTime) {
        $membershipStatus = 'Expired';
        $remainingTime = 'Expired';
    }

} else {
    $membershipStatus = 'No membership found';
    $remainingTime = '';
}

// Close the statement
$stmt->close();

// Close the connection
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

                <!-- Membership Status -->
                <div class="card">
                    <div class="card-header">
                        Membership Status
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $membershipStatus; ?></h5>
                        <?php if ($membershipStatus == 'Active'): ?>
                            <p class="card-text">Your membership is active and valid until: <?php echo $membershipEndDate; ?></p>
                            <p class="card-text">Time remaining until expiration: <?php echo $remainingTime; ?></p>
                        <?php elseif ($membershipStatus == 'Expired'): ?>
                            <p class="card-text">Your membership has expired.</p>
                        <?php else: ?>
                            <p class="card-text">No active membership found.</p>
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