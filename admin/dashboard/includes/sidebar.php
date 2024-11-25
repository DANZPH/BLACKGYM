<div class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item ">
            <img src="../../../img/logo.jpg" alt="Logo" class="img-fluid" style="max-width: 250px;  position: relative; bottom: 20px; border: solid;">
        </li>
      
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-users"></i> Manage Members
            </a>
            <div id="manageMembers" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="view_member.php" class="nav-link">View Members</a></li>
                    <li class="nav-item"><a href="add_member.php" class="nav-link">Add Member</a></li>
                    <li class="nav-item"><a href="update_member.php" class="nav-link">Update Member</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#manageAttendance" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-calendar-check"></i> Manage Attendance
            </a>
            <div id="manageAttendance" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="attendance.php" class="nav-link">Check-in/Check-out</a></li>
                    <li class="nav-item"><a href="view_attendance.php" class="nav-link">View Attendance</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#managePayments" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-credit-card"></i> Payments
            </a>
            <div id="managePayments" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="payments.php" class="nav-link">Pay/Cancel</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#reports" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-chart-bar"></i> Reports
            </a>
            <div id="reports" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="chart_report.php" class="nav-link">Chart Representation</a></li>
                    <li class="nav-item"><a href="members_report.php" class="nav-link">Members Report</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#transactions" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-exchange-alt"></i> Transactions
            </a>
            <div id="transactions" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="payment_history.php" class="nav-link">Payments</a></li>
                    <li class="nav-item"><a href="receipt_transaction.php" class="nav-link">Receipts</a></li>
                </ul>
            </div>
        </li>
    </ul>
</div>