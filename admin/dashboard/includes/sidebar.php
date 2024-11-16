<div id="sidebar-wrapper" class="col-lg-3">
    <div class="container">


        <ul class="list-unstyled mt-3 collapse" id="sidebarMenu">
            <!-- Manage Members Dropdown -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-users"></i> Manage Members
                </a>
                <div id="manageMembers" class="collapse">
                    <ul class="list-unstyled pl-3">
                        <li><a href="view_member.php" class="nav-link"><i class="fa fa-eye"></i> View Members</a></li>
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
                <div id="manageAttendance" class="collapse">
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
                <div id="managePayments" class="collapse">
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
                <div id="reports" class="collapse">
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
                <div id="transactions" class="collapse">
                    <ul class="list-unstyled pl-3">
                        <li><a href="payment_transaction.php" class="nav-link"><i class="fa fa-credit-card"></i> Payment</a></li>
                        <li><a href="receipt_transaction.php" class="nav-link"><i class="fa fa-file-invoice"></i> Receipt</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>