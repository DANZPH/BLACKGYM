<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';
// Fetch the MemberID from the session
$memberID = $_SESSION['MemberID'];  // Use session member_id instead of MemberID
// Fetch the Membership data from the database
$sql = "SELECT EndDate, Status FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("d", $memberID); // Binding MemberID parameter
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html, body {
            background: linear-gradient(90deg, #f8f9fa, #eef2f3);
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: linear-gradient(90deg, #007bff, #6a11cb);
            color: white;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .content-container {
            margin-top: 80px; /* Adjust based on header height */
            padding: 20px;
        }

        .membership-card {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .membership-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .membership-card h5 {
            font-weight: bold;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: bold;
            color: white;
        }

        .status-active .status-badge {
            background-color: #28a745;
        }

        .status-expired .status-badge {
            background-color: #dc3545;
        }

        .status-pending .status-badge {
            background-color: #ffc107;
            color: black;
        }

        .status-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #6c757d;
        }

        .status-expired .status-icon {
            color: #dc3545;
        }

        .status-pending .status-icon {
            color: #ffc107;
        }

        .status-actions {
            margin-top: 20px;
        }

        .btn-renew {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 20px;
        }

        .sidebar {
            background: linear-gradient(90deg, #6a11cb, #007bff);
            color: white;
            padding: 20px;
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            margin-top: 70px;
            width: 250px;
            z-index: 1;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .sidebar a:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <header>
        <h1>BLACKGYM Member Dashboard</h1>
    </header>

    <div class="d-flex">
        <aside class="sidebar">
            <a href="#">Dashboard</a>
            <a href="#">Membership</a>
            <a href="#">Payments</a>
            <a href="#">Attendance</a>
            <a href="#">Profile</a>
        </aside>

        <div class="container content-container">
            <h2>Hi, <?php echo $_SESSION['username']; ?>!</h2>
            <p>Welcome to your BLACKGYM dashboard. Manage your membership, payments, and attendance.</p>

            <!-- Membership Status Section -->
            <?php if ($membershipStatus === 'Active'): ?>
                <div class="card membership-card status-active">
                    <div class="card-header">
                        <span class="status-badge">Active</span>
                    </div>
                    <div class="card-body">
                        <h5>Membership Valid</h5>
                        <p>Valid until: <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong></p>
                        <div class="remaining-time">
                            Time remaining: <span><?php echo $remainingTime; ?></span>
                        </div>
                    </div>
                </div>
            <?php elseif ($membershipStatus === 'Expired'): ?>
                <div class="card membership-card status-expired">
                    <div class="text-center">
                        <div class="status-icon">&#x26A0;</div>
                        <h5>Membership Expired</h5>
                        <p>Your membership expired on <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
                        <div class="status-actions">
                            <a href="renew.php" class="btn btn-renew">Renew Membership</a>
                        </div>
                    </div>
                </div>
            <?php elseif ($membershipStatus === 'Pending'): ?>
                <div class="card membership-card status-pending">
                    <div class="text-center">
                        <div class="status-icon">&#x1F6A8;</div>
                        <h5>Membership Pending</h5>
                        <p>Your membership is pending. Please complete payment.</p>
                        <div class="status-actions">
                            <a href="payment.php" class="btn btn-primary">Make Payment</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card membership-card">
                    <h5>No Membership</h5>
                    <p>You don't have an active membership. Please contact support.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
