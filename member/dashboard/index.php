<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- SweetAlert2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="includes/styles.css">
	<title>BLACKGYM</title>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';

// Fetch the MemberID from the session
$memberID = $_SESSION['MemberID'];
$sql = "SELECT EndDate, Status FROM Membership WHERE MemberID = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("d", $memberID);
$stmt->execute();
$stmt->bind_result($endDate, $membershipStatus);
$stmt->fetch();
$stmt->close();

$currentDate = new DateTime();
$remainingTime = isset($endDate) 
    ? (new DateTime($endDate))->diff($currentDate)->format('%m months, %d days') 
    : "No expiration date set.";

include 'includes/sidebar.php';
?>
<!-- CONTENT -->
<section id="content">
<?php include 'includes/header.php'; ?>
	<!-- MAIN -->
	<main>
		<div class="head-title">
			<div class="left">
				<h1>Dashboard</h1>
				<ul class="breadcrumb">
					<li>
						<a href="#">Dashboard</a>
					</li>
					<li><i class='bx bx-chevron-right'></i></li>
					<li>
						<a class="active" href="#">Home</a>
					</li>
				</ul>
			</div>
			<a href="#" class="btn-download">
				<i class='bx bxs-cloud-download'></i>
				<span class="text">Download PDF</span>
			</a>
		</div>

		<ul class="box-info">
			<li>
				<div class="col-md-9">
					<h2>Hi, <?php echo $_SESSION['username']; ?>!</h2>
					<p>Here you can view and manage your BLACKGYM membership, payments, and attendance.</p>
					
					<!-- Membership Status Section -->
					<?php if ($membershipStatus === 'Active'): ?>
						<div class="card membership-card">
							<div class="card-header">
								<h5>Membership Status</h5>
							</div>
							<div class="card-body">
								<h5 class="card-title status-active">Active Membership</h5>
								<p class="card-text">Your membership is valid until <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
								<div class="remaining-time">
									<p>Time remaining: <span><?php echo $remainingTime; ?></span></p>
								</div>
							</div>
						</div>
					<?php elseif ($membershipStatus === 'Expired'): ?>
						<div class="card membership-card status-expired">
							<div class="text-center">
								<div class="status-icon">&#x26A0;</div>
								<h5 class="card-title">Membership Expired</h5>
								<p class="card-text">Your membership expired on <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
								<p>Please renew your membership to regain access to BLACKGYM facilities.</p>
							</div>
							<div class="status-actions text-center">
								<a href="renew.php" class="btn btn-renew">Renew Membership</a>
							</div>
						</div>
					<?php elseif ($membershipStatus === 'Pending'): ?>
						<div class="card membership-card status-pending">
							<div class="text-center">
								<div class="status-icon">&#x1F6A8;</div>
								<h5 class="card-title">Membership Pending</h5>
								<p>Your membership is currently pending. Please make the necessary payment and wait for approval.</p>
							</div>
							<div class="status-actions text-center">
								<a href="payment.php" class="btn btn-primary">Make Payment</a>
							</div>
						</div>
					<?php else: ?>
						<div class="card membership-card">
							<div class="card-body">
								<h5 class="card-title">No Active Membership</h5>
								<p class="card-text">It seems you don't have an active membership at the moment. Please renew or contact support for assistance.</p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</li>
		</ul>

		<div class="table-data">
			<div class="order">
				<div class="head">
					<h3>Recent Orders</h3>
					<i class='bx bx-search'></i>
					<i class='bx bx-filter'></i>
				</div>
				<table>
					<thead>
						<tr>
							<th>User</th>
							<th>Date Order</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<img src="img/people.png">
								<p>John Doe</p>
							</td>
							<td>01-10-2021</td>
							<td><span class="status completed">Completed</span></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</main>
	<!-- MAIN -->
</section>
<!-- CONTENT -->
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../action/logout.php';
            }
        });
    }
</script>
<script src="includes/script.js"></script>
</body>
</html>