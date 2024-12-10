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
	 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
include 'includes/sidebar.php';
?>
	<!-- CONTENT -->
	<section id="content">
<?php
include 'includes/header.php';
?>
		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Hi, <?php echo $_SESSION['username']; ?></h1>
					
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Home</a>
						</li>
					</ul>
				</div>
				<a href="#" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">Download PDF</span>
				</a>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
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
                            <div class="status-icon">&#x26A0; <!-- Warning Symbol --></div>
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
                            <div class="status-icon">&#x1F6A8; <!-- Emergency Light Symbol --></div>
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
					</span>
				</li>
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3>2834</h3>
						<p>Visitors</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
						<h3>$2543</h3>
						<p>Total Sales</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Orders</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
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
							<tr>
								<td>
									<img src="img/people.png">
									<p>John Doe</p>
								</td>
								<td>01-10-2021</td>
								<td><span class="status pending">Pending</span></td>
							</tr>
							<tr>
								<td>
									<img src="img/people.png">
									<p>John Doe</p>
								</td>
								<td>01-10-2021</td>
								<td><span class="status process">Process</span></td>
							</tr>
							<tr>
								<td>
									<img src="img/people.png">
									<p>John Doe</p>
								</td>
								<td>01-10-2021</td>
								<td><span class="status pending">Pending</span></td>
							</tr>
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
				<div class="todo">
					<div class="head">
						<h3>Todos</h3>
						<i class='bx bx-plus' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<ul class="todo-list">
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
					</ul>
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
	
	
	
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
