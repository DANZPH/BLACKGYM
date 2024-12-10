<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';
// Fetch the MemberID from the session
$memberID = $_SESSION['MemberID'];  // Use session member_id instead of MemberID



$query = "SELECT p.PaymentDate, p.Amount, p.PaymentMethod, p.Status, u.Username 
          FROM Payments p 
          INNER JOIN Members m ON p.MemberID = m.MemberID
          INNER JOIN Users u ON m.UserID = u.UserID
          WHERE p.MemberID = ? ORDER BY p.PaymentDate DESC";
// Prepare the query and bind the member ID
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $memberID);
$stmt->execute();
$result = $stmt->get_result();

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
include 'includes/na.php';
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
				<a href="../action/fetch_receipt.php" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">QRPASS</span>
				</a>
			</div>

			<ul class="box-info">
				<li>
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<span class="text">
    <!-- Membership Status Section -->
    <?php if ($membershipStatus === 'Active'): ?>
        <div>
            <p>Valid until <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
            <p>Time remaining: <span><?php echo $remainingTime; ?></span></p>
        </div>
    <?php elseif ($membershipStatus === 'Expired'): ?>
        <div>
            <p>&#x26A0; Membership Expired</p> <!-- Warning Symbol -->
            <p>Expired on <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
            <p>Please renew your membership to regain access to BLACKGYM facilities.</p>
            <a href="renew.php">Renew Membership</a>
        </div>
    <?php elseif ($membershipStatus === 'Pending'): ?>
        <div>
            <p>&#x1F6A8; Membership Pending</p> <!-- Emergency Light Symbol -->
            <p>Your membership is currently pending. Please make the necessary payment and wait for approval.</p>
            <a href="payment.php">Make Payment</a>
        </div>
    <?php else: ?>
        <div>
            <h5>No Active Membership</h5>
            <p>It seems you don't have an active membership at the moment. Please renew or contact support for assistance.</p>
        </div>
    <?php endif; ?>
</span>
					</span>
				</li>
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3>0/50</h3>
						<p>Current people</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
						<h3>₱</h3>
						<p>Total Spend</p>
					</span>
				</li>
			</ul>


<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Payment History</h3>
            <i class='bx bx-search'></i>
            <i class='bx bx-filter'></i>
        </div>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="img/people.png" alt="User">
                            <p><?php echo htmlspecialchars($row['Username']); ?></p>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($row['PaymentDate'])); ?></td>
                        <td><?php echo number_format($row['Amount'], 2); ?></td>
                        <td>
                            <span class="status <?php echo strtolower($row['Status']); ?>">
                                <?php echo ucfirst($row['Status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
