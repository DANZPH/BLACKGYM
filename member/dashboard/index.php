<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';

// Fetch the MemberID from the session
$memberID = $_SESSION['MemberID'];

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

// Fetch payment history
$paymentSql = "SELECT Amount, PaymentDate, PaymentType, ReceiptNumber FROM Payments WHERE MemberID = ? ORDER BY PaymentDate DESC";
$paymentStmt = $conn1->prepare($paymentSql);
$paymentStmt->bind_param("d", $memberID);
$paymentStmt->execute();
$paymentStmt->bind_result($amount, $paymentDate, $paymentMethod, $receiptNumber);
$payments = [];
while ($paymentStmt->fetch()) {
    $payments[] = [
        'amount' => $amount,
        'paymentDate' => $paymentDate,
        'paymentMethod' => $paymentMethod,
        'receiptNumber' => $receiptNumber
    ];
}
$paymentStmt->close();
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
    <?php include 'includes/sidebar.php'; ?>
    <!-- CONTENT -->
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
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
                        <!-- Membership Status Section -->
                        <?php if ($membershipStatus === 'Active'): ?>
                            <div>
                                <p>Until <strong><?php echo date('d M Y', strtotime($endDate)); ?></strong>.</p>
                                <p> <span><?php echo $remainingTime; ?></span></p>
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
                </li>
  				      <li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3>0/50</h3>
						<p>Gym User</p>
					</span>
				</li>
			        	<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
						<h3>₱00.00</h3>
						<p>ballance</p>
					</span>
				</li>
            </ul>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Payment History</h3>
                        <i class='bx bx-search' ></i>
                        <i class='bx bx-filter' ></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>‎ Payment Date</th>‎ 
                                <th>Payment Method</th>
                                <th>Receipt Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td>₱<?php echo number_format($payment['amount']); ?></td>
                                    <td>‎ <?php echo date('d M Y', strtotime($payment['paymentDate'])); ?></td>
                                    <td><?php echo $payment['paymentMethod']; ?></td>
                                    <td><?php echo $payment['receiptNumber']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="4">No payment history found.</td>
                                </tr>
                            <?php endif; ?>
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