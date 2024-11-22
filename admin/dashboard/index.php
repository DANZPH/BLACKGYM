<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php');
    exit();
}
include '../../database/connection.php';

// Total Members with Active Membership Status
$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM Members WHERE MembershipStatus = 'Active'";
$totalMembersResult = $conn1->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

// Total Payments
$totalPaymentsQuery = "SELECT SUM(Amount) AS total_amount FROM Payments";
$totalPaymentsResult = $conn1->query($totalPaymentsQuery);
$totalPayments = $totalPaymentsResult->fetch_assoc()['total_amount'];

// Total Pending Payments (Payments related to Pending Membership)
$pendingPaymentsQuery = "SELECT COUNT(*) AS Status FROM Membership WHERE Status = 'Pending'";
$pendingPaymentsResult = $conn1->query($pendingPaymentsQuery);
$pendingPayments = $pendingPaymentsResult->fetch_assoc()['Status'];

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <style>
    
    html, body {
             
    padding-top: 30px;
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            width: 250px;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .navbar {
            padding: 0.75rem 1rem;
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1; /* Ensures it expands to fit available space */
        }
        .card {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .monitor-card {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-nav .nav-link {
            color: #fff;
        }
        /* Fix for the DataTable clipping */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .dataTables_wrapper {
            padding: 20px;
        }

/* Sticky Navbar */
.navbar {
  background: black;
}
.sticky-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999; /* Keep navbar above other content */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

}
.table-responsive {
    overflow-x: auto;
}

/*footer*/
.content-wrapper {
    flex: 1;  
}

.footer {
    background-color: #343a40;
    color: #fff;
    text-align: center;
    padding: 10px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}
  </style>
<?php include '../../includes/head.php'; ?>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="container mt-5">
            <h1 class="text-center mb-4">BLACKGYM DASHBOARD</h1>
            <p class="text-center">Monitor and manage system activities below.</p>

            <!-- Dashboard Cards -->
            <div class="row mt-4">
                <!-- Active Members Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">
                                    <i class="fas fa-users text-primary"></i> Active Members
                                </h4>
                                <h2 class="card-text text-primary"><?php echo $totalMembers; ?></h2>
                            </div>
                            <a href="members.php" class="btn btn-outline-info btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">
                                    <i class="fas fa-credit-card text-success"></i> Earnings
                                </h4>
                                <h2 class="card-text text-success">₱<?php echo number_format($totalPayments, 2); ?></h2>
                            </div>
                            <a href="payments.php" class="btn btn-outline-success btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <!-- Pending Memberships Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">
                                    <i class="fas fa-clock text-warning"></i> Pending
                                </h4>
                                <h2 class="card-text text-warning"><?php echo $pendingPayments; ?></h2>
                            </div>
                            <a href="pending_memberships.php" class="btn btn-outline-warning btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    <?php include '../../includes/footer.php'; ?>


    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
