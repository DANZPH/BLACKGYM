<div class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item ">
            <img src="../../../img/logo.jpg" alt="Logo" class="img-fluid" style="max-width: 250px;  position: relative; bottom: 20px; border: solid;">
        </li>
      
        <li class="nav-item">
            <a class="nav-link" href="index">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-users"></i> Manage Members
            </a>
            <div id="manageMembers" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="view_member" class="nav-link">View Members</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#manageAttendance" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-calendar-check"></i> Manage Attendance
            </a>
            <div id="manageAttendance" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="attendance" class="nav-link">Check-in/Check-out</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#managePayments" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-credit-card"></i> Payments
            </a>
            <div id="managePayments" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="payments" class="nav-link">Pay/Cancel/Renew</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#reports" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-chart-bar"></i> Reports
            </a>
            <div id="reports" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="chart_report" class="nav-link">Chart Representation</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#transactions" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-exchange-alt"></i> Transactions
            </a>
            <div id="transactions" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="payment_history" class="nav-link">History</a></li>
                    <li class="nav-item"><a href="receipt_transaction" class="nav-link">Receipts</a></li>
                </ul>
            </div>
                    <li class="nav-item">
            <a class="nav-link" href="#" onclick="confirmLogout()">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </li>
        </li>
    </ul>
</div>