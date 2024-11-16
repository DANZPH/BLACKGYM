<?php include 'includes/header.php'; ?>

<style>
    .monitor-card {
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .monitor-card .card-header {
        background-color: #343a40;
        color: #fff;
        font-weight: bold;
    }

    .monitor-card .card-body {
        background-color: #f8f9fa;
        text-align: center;
    }

    .monitor-card h2 {
        font-size: 3rem;
        margin-top: 20px;
        font-weight: bold;
    }

    .container {
        padding-top: 20px;
    }

    .btn-info {
        margin-top: 10px;
    }

    .btn-warning {
        margin-top: 10px;
    }
</style>

<div class="d-flex">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Monitor and manage system activities below.</p>

        <!-- Monitoring Section -->
        <div class="row mt-4">
            <!-- Total Members with Active Status -->
            <div class="col-md-4">
                <div class="card monitor-card shadow-sm">
                    <div class="card-header">
                        <h4>Total Active Members</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <h2><?php echo $totalMembers; ?></h2>
                        </div>
                        <div>
                            <a href="members.php" class="btn btn-info btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Payments -->
            <div class="col-md-4">
                <div class="card monitor-card shadow-sm">
                    <div class="card-header">
                        <h4>Total Payments</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <h2>₱<?php echo number_format($totalPayments, 2); ?></h2>
                        </div>
                        <div>
                            <a href="payments.php" class="btn btn-info btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Payments (Based on Pending Membership Status) -->
            <div class="col-md-4">
                <div class="card monitor-card shadow-sm">
                    <div class="card-header">
                        <h4>Pending Memberships</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <h2><?php echo $pendingPayments; ?></h2>
                        </div>
                        <div>
                            <a href="pending_memberships.php" class="btn btn-warning btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Content Can Go Here -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>