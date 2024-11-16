<div class="sidebar">
    <div class="container">
        <!-- Admin Dashboard Links -->
        <ul class="list-unstyled">
            <!-- Manage Members Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-users"></i> Manage Members
                </a>
                <div id="manageMembers" class="collapse" data-parent="#sidebar">
                    <ul class="list-unstyled pl-3">
                        <li><a href="view_members.php" class="nav-link"><i class="fa fa-eye"></i> View Members</a></li>
                        <li><a href="add_member.php" class="nav-link"><i class="fa fa-user-plus"></i> Add Member</a></li>
                        <li><a href="update_member.php" class="nav-link"><i class="fa fa-edit"></i> Update Members</a></li>
                    </ul>
                </div>
            </li>

            <!-- Manage Attendance Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#manageAttendance" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-calendar-check"></i> Manage Attendance
                </a>
                <div id="manageAttendance" class="collapse" data-parent="#sidebar">
                    <ul class="list-unstyled pl-3">
                        <li><a href="checkin_checkout.php" class="nav-link"><i class="fa fa-check-circle"></i> Check-in/Check-out</a></li>
                        <li><a href="view_attendance.php" class="nav-link"><i class="fa fa-eye"></i> View Attendance</a></li>
                    </ul>
                </div>
            </li>

            <!-- Payment Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#managePayments" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-credit-card"></i> Payment
                </a>
                <div id="managePayments" class="collapse" data-parent="#sidebar">
                    <ul class="list-unstyled pl-3">
                        <li><a href="pay_cancel.php" class="nav-link"><i class="fa fa-money-bill"></i> Pay/Cancel</a></li>
                    </ul>
                </div>
            </li>

            <!-- Reports Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#reports" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-chart-bar"></i> Report
                </a>
                <div id="reports" class="collapse" data-parent="#sidebar">
                    <ul class="list-unstyled pl-3">
                        <li><a href="chart_report.php" class="nav-link"><i class="fa fa-chart-line"></i> Chart Representation</a></li>
                        <li><a href="members_report.php" class="nav-link"><i class="fa fa-users"></i> Members Report</a></li>
                    </ul>
                </div>
            </li>

            <!-- Transaction Dropdown -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#transactions" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-exchange-alt"></i> Transaction
                </a>
                <div id="transactions" class="collapse" data-parent="#sidebar">
                    <ul class="list-unstyled pl-3">
                        <li><a href="payment_transaction.php" class="nav-link"><i class="fa fa-credit-card"></i> Payment</a></li>
                        <li><a href="receipt_transaction.php" class="nav-link"><i class="fa fa-file-invoice"></i> Receipt</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>

<!-- Add Custom CSS to make the collapse horizontal -->
<style>
    .sidebar {
        width: 250px;
    }

    .collapse {
        display: none; /* Hidden by default */
    }

    .collapse.show {
        display: block;
    }

    /* Custom CSS to style the horizontal collapse */
    .nav-item {
        position: relative;
    }

    .collapse {
        position: absolute;
        top: 0;
        left: 100%;
        width: 250px; /* Adjust width as needed */
        margin-left: 5px;
        border-left: 1px solid #ddd;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .collapse ul {
        padding-left: 10px;
    }
</style>

<!-- Add necessary JS dependencies for Bootstrap collapse functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>