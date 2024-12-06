<div class="sidebar">
    <ul class="nav flex-column">
        <!-- Logo -->
        <li class="nav-item">
            <img src="../../../img/logo.jpg" alt="Logo" class="img-fluid" style="max-width: 250px; position: relative; bottom: 20px; border: solid;">
        </li>
        
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="index">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <!-- Manage Members -->
        <li class="nav-item">
            <a class="nav-link" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-users"></i> Manage Members
            </a>
            <div id="manageMembers" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="view_member" class="nav-link">View Members</a></li>
                    <li class="nav-item"><a href="update_member" class="nav-link">Update Member</a></li>
                </ul>
            </div>
        </li>
        
        <!-- Manage Attendance -->
        <li class="nav-item">
            <a class="nav-link" href="#manageAttendance" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-calendar-check"></i> Manage Attendance
            </a>
            <div id="manageAttendance" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="attendance" class="nav-link">Check-in/Check-out</a></li>
                    <li class="nav-item"><a href="view_attendance" class="nav-link">View Attendance</a></li>
                </ul>
            </div>
        </li>
        
        <!-- Payments -->
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
        
        <!-- Reports -->
        <li class="nav-item">
            <a class="nav-link" href="#reports" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-chart-bar"></i> Reports
            </a>
            <div id="reports" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="chart_report" class="nav-link">Chart Representation</a></li>
                    <li class="nav-item"><a href="members_report" class="nav-link">Members Report</a></li>
                </ul>
            </div>
        </li>
        
        <!-- Transactions -->
        <li class="nav-item">
            <a class="nav-link" href="#transactions" data-toggle="collapse" aria-expanded="false">
                <i class="fa fa-exchange-alt"></i> Transactions
            </a>
            <div id="transactions" class="collapse">
                <ul class="nav flex-column ml-3">
                    <li class="nav-item"><a href="payment_history" class="nav-link">Payments</a></li>
                    <li class="nav-item"><a href="receipt_transaction" class="nav-link">Receipts</a></li>
                </ul>
            </div>
        </li>
        
        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="confirmLogout()">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                window.location.href = '../action/logout_process.php';
            }
        });
    }
</script>