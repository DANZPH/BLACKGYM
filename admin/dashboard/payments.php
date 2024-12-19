<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    header('Location: login.php'); 
    exit();
}
include '../../database/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../../img/favicon-512x512.png" sizes="512x512" type="image/png">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="includes/JS/sweetalert.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/styles.css">
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
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
            </div>

<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <div class="table">
            <table id="paymentsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="display:none;">Member ID</th> <!-- Hidden column for MemberID -->
                        <th>Username</th>
                        <th>Email</th>
                        <th>Membership Status</th>
                        <th>Subscription</th>
                        <th>Session Price</th>
                        <th>Total Bill</th>
                        <th>Balance</th> <!-- New column for Balance -->
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.MembershipStatus, 
                            Membership.Subscription, Membership.SessionPrice, 
                            (Membership.Subscription + Membership.SessionPrice) AS TotalBill,
                            Members.Balance, 
                            CASE 
                                WHEN Membership.Status = 'Active' THEN 'Active'
                                WHEN Membership.Status = 'Pending' THEN 'Pending'
                                WHEN Membership.Status = 'Expired' THEN 'Expired'
                            END AS Status
                            FROM Members 
                            INNER JOIN Users ON Members.UserID = Users.UserID
                            LEFT JOIN Membership ON Members.MemberID = Membership.MemberID";
                    $result = $conn1->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Membership Status color
                            $statusClass = '';
                            if ($row['Status'] == 'Active') {
                                $statusClass = 'text-success'; // Green for Active
                            } elseif ($row['Status'] == 'Pending') {
                                $statusClass = 'text-warning'; // Orange for Pending
                            } elseif ($row['Status'] == 'Expired') {
                                $statusClass = 'text-danger'; // Red for Expired
                            }

                            // Balance color
                            $balanceClass = ($row['Balance'] < 0) ? 'text-danger' : 'text-success'; // Red for negative, green for positive

                            // Truncate email and add tooltip
                            $emailDisplay = (strlen($row['Email']) > 30) ? substr($row['Email'], 0, 30) . '...' : $row['Email'];
                            $emailTitle = $row['Email']; // Full email to show in tooltip

                            echo "<tr>
                                <td style='display:none;'>{$row['MemberID']}</td> <!-- Hidden MemberID -->
                                <td>{$row['Username']}</td>
                                <td><span title='{$emailTitle}'>{$emailDisplay}</span></td>
                                <td>{$row['MembershipStatus']}</td>
                                <td>" . number_format($row['Subscription'], 2) . "</td>
                                <td>" . number_format($row['SessionPrice'], 2) . "</td>
                                <td>" . number_format($row['TotalBill'], 2) . "</td>
                                <td class='{$balanceClass}'>" . number_format($row['Balance'], 2) . "</td>
                                <td class='{$statusClass}'><strong>{$row['Status']}</strong></td>
                                <td><button class='btn btn-success pay-btn' data-memberid='{$row['MemberID']}' 
                                    data-totalbill='{$row['TotalBill']}' data-balance='{$row['Balance']}'>Pay</button>
                                       <button class='btn btn-danger refund-btn' data-memberid='{$row['MemberID']}'>Refund</button>
</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-center'>No members found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .text-success {
        color: #28a745 !important; 
    }
    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    td span[title] {
        text-decoration: underline;
        cursor: pointer;
    }

    td span[title]:hover {
        background-color: #f1f1f1;
    }
    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
            <!-- Modal for Payment -->
            <?php include 'includes/modal/pay.php'; ?>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable with scroll and column hiding for MemberID
    $('#paymentsTable').DataTable({
        scrollX: true,
        columnDefs: [
            {
                targets: [0], // Target the first column (MemberID) to hide it
                visible: false, // Hide the MemberID column
            }
        ]
    });

    // Event delegation for the 'Pay' button click
    $(document).on('click', '.pay-btn', function () {
        var memberID = $(this).data('memberid');
        var totalBill = $(this).data('totalbill');
        var balance = $(this).data('balance');  // Get the balance from the table

        // Fill the modal inputs with the corresponding data
        $('#memberID').val(memberID);
        $('#amount').val(totalBill);  // Set the amount to the total bill
        $('#balance').val(balance);  // Set the balance value
        $('#paymentModal').modal('show'); // Show the modal
    });

    // Event delegation for the 'Refund' button click
    $(document).on('click', '.refund-btn', function () {
        var memberID = $(this).data('memberid');

        // Send the memberID to refund_process.php using AJAX
        $.ajax({
            url: '../action/refund_process.php',
            type: 'POST',
            data: { memberID: memberID },
            success: function(response) {
                alert(response);  // Show the response from the server
                location.reload(); // Reload the page to show updated data
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);  // Log the error if something went wrong
                alert('Error: ' + error);
            }
        });
    });

    // Payment Type change event to update amount based on Balance or Cash
    $('#paymentType').change(function () {
        var paymentType = $(this).val();
        var balance = parseFloat($('#balance').val()) || 0;
        
        if (paymentType === "Balance" && balance > 0) {
            $('#amountPaid').val(balance); // Set Amount Paid to balance if Balance is selected
            $('#amount').val($('#amount').val());  // Keep Amount the same as Total Bill (do not modify)
            updateTotalAmount(); // Recalculate total amount based on amount
        } else {
            updateTotalAmount(); // Recalculate total amount if Cash is selected
        }
    });

    // Event listener for multiplier input to update total amount
    $('#multiplier').on('input', function () {
        updateTotalAmount();
    });

    // Event listener for amount input to update total amount
    $('#amount').on('input', function () {
        updateTotalAmount();
    });

    // Function to update the total amount based on multiplier or amount
    function updateTotalAmount() {
        var amount = parseFloat($('#amount').val()) || 0;
        var multiplier = parseFloat($('#multiplier').val()) || 1;
        var totalAmount = amount * multiplier;
        $('#totalAmount').val(totalAmount.toFixed(2));
        calculateChange();
    }

    // Function to calculate change after payment
    function calculateChange() {
        var amountPaid = parseFloat($('#amountPaid').val()) || 0;
        var totalAmount = parseFloat($('#totalAmount').val()) || 0;
        var change = amountPaid - totalAmount;
        $('#change').val(change.toFixed(2));  // Display the calculated change

        // Allow for negative change (if Amount Paid < Total Amount)
    }

    // Form submission to process payment
    $('#paymentForm').submit(function (e) {
        e.preventDefault();

        var amount = parseFloat($('#amount').val());
        var amountPaid = parseFloat($('#amountPaid').val());
        var change = parseFloat($('#change').val());
        var addToBalance = $('#addToBalance').val();
        var memberID = $('#memberID').val();

        // Prevent form submission if Amount Paid is less than Total Bill and no balance is added
        if (amountPaid < amount && addToBalance === 'no') {
            alert("Error: Amount Paid cannot be less than the Total Bill unless you add the negative balance to your Balance.");
            return; // Prevent form submission
        }

        var formData = $(this).serialize();

        // Ajax request to process payment
        $.ajax({
            url: '../action/payment_process.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                alert(response);
                $('#paymentModal').modal('hide');
                location.reload(); // Reload the page to show updated payments
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>

    <script src="includes/script.js"></script>
</body>
</html>
