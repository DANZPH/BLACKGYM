<!-- index.php -->
<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: login.php');
    exit();
}

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

                <!-- Example of Content, You can replace this with dynamic content -->
                <div class="card">
                    <div class="card-header">
                        Membership Status
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Active Membership</h5>
                        <p class="card-text">Your membership is active and valid until 31st December 2024.</p>
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